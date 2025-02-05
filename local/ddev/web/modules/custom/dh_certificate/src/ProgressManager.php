<?php

namespace Drupal\dh_certificate;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\State\StateInterface;
use Drupal\user\UserInterface;
use Drupal\dh_certificate\Entity\CourseInterface;
use Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface;
use Drupal\Core\Database\Connection;

/**
 * Service for managing certificate progress.
 */
class ProgressManager implements ProgressManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The requirement type manager.
   *
   * @var \Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface
   */
  protected $requirementTypeManager;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a ProgressManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface $requirement_type_manager
   *   The requirement type manager.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    StateInterface $state,
    RequirementTypeManagerInterface $requirement_type_manager,
    Connection $database
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->state = $state;
    $this->requirementTypeManager = $requirement_type_manager;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserProgress(AccountInterface $account) {
    if (!$account) {
      return NULL;
    }

    $uid = $account->id();
    $courses = $this->getUserCourses($uid);
    
    // Debug logging
    \Drupal::logger('dh_certificate')->debug('Processing progress for user @uid with @count courses', [
      '@uid' => $uid,
      '@count' => count($courses)
    ]);

    // Calculate totals
    $total_courses = count($courses);
    $completed_courses = 0;
    $total_credits = 0;
    $completed_credits = 0;

    foreach ($courses as $course) {
      $total_credits += $course['credits'];
      if ($course['status'] === 'completed') {
        $completed_courses++;
        $completed_credits += $course['credits'];
      }
    }

    return [
      'total_courses' => $total_courses,
      'completed_courses' => $completed_courses,
      'total_credits' => $total_credits,
      'completed_credits' => $completed_credits,
      'required_credits' => 12, // You might want to make this configurable
      'total_percentage' => $total_courses ? round(($completed_courses / $total_courses) * 100) : 0,
      'courses' => $courses,
      'pending_actions' => $this->getPendingActionsCount($uid),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getUserProgressById($uid) {
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->condition('status', 1)
      ->condition('field_course_meets_what_requirem', NULL, 'IS NOT NULL')  // Only get courses with requirements
      ->sort('created', 'ASC')
      ->accessCheck(TRUE); // Add explicit access check
    $course_ids = $query->execute();
    
    $progress = $this->state->get('dh_certificate.progress.' . $uid, []);
    $result = [];
    
    if (!empty($course_ids)) {
      $courses = $this->entityTypeManager->getStorage('node')->loadMultiple($course_ids);
      foreach ($courses as $course) {
        $course_progress = $progress[$course->id()] ?? [];
        
        // Get taxonomy term data
        $term_fields = [
          'field_department_or_school',
          'field_course_year',
          'field_semester',
          'field_dh_concentration',
          'field_dh_research_data_type',
          'field_course_meets_what_requirem',
        ];
        
        $taxonomy_data = [];
        foreach ($term_fields as $field_name) {
          if ($course->hasField($field_name) && !$course->get($field_name)->isEmpty()) {
            $term = $course->get($field_name)->entity;
            if ($term) {
              $taxonomy_data[$field_name] = [
                'tid' => $term->id(),
                'name' => $term->label(),
                'vid' => $term->bundle(),
              ];
            }
          }
        }

        $result[$course->id()] = [
          'nid' => $course->id(),  // Add explicit node ID
          'title' => $course->label(),
          'mnemonic' => $course->hasField('field_course_mnemonic') ? 
            $course->get('field_course_mnemonic')->value : '',
          'semester' => $taxonomy_data['field_semester'] ?? [],
          'year' => $taxonomy_data['field_course_year'] ?? [],
          'department' => $taxonomy_data['field_department_or_school'] ?? [],
          'concentration' => $taxonomy_data['field_dh_concentration'] ?? [],
          'research_data_type' => $taxonomy_data['field_dh_research_data_type'] ?? [],
          'meets_requirement' => $taxonomy_data['field_course_meets_what_requirem'] ?? NULL,
          'instructor' => $course->hasField('field_course_instructor') ? 
            $course->get('field_course_instructor')->entity?->label() : '',
          'description' => $course->hasField('field_short_text_description') ? 
            $course->get('field_short_text_description')->value : '',
          'completed' => $course_progress['completed'] ?? FALSE,
          'progress' => $course_progress['progress'] ?? 0,
          'timestamp' => $course_progress['timestamp'] ?? 0,
        ];
      }
    }
    
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getCourseProgress(CourseInterface $course, AccountInterface $account) {
    return $this->getProgress($course->id(), $account);
  }

  /**
   * {@inheritdoc}
   */
  public function updateCourseProgress(NodeInterface $course, AccountInterface $account, array $progress) {
    $user_progress = $this->state->get('dh_certificate.progress.' . $account->id(), []);
    $user_progress[$course->id()] = $progress;
    $this->state->set('dh_certificate.progress.' . $account->id(), $user_progress);
  }

  /**
   * {@inheritdoc}
   */
  public function getProgress($id, AccountInterface $account) {
    $progress = $this->state->get('dh_certificate.progress.' . $account->id(), []);
    return $progress[$id] ?? [
      'completed' => FALSE,
      'progress' => 0,
      'timestamp' => 0,
      'lessons_completed' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function updateProgress($id, AccountInterface $account, array $progress) {
    // First update state-based storage
    $user_progress = $this->state->get('dh_certificate.progress.' . $account->id(), []);
    $user_progress[$id] = $progress;
    $this->state->set('dh_certificate.progress.' . $account->id(), $user_progress);

    // Then update entity-based storage if available
    try {
      $user = $this->entityTypeManager->getStorage('user')->load($account->id());
      if ($user instanceof UserInterface) {
        $progress_entity = $this->getOrCreateProgressEntity($user);
        if ($progress_entity && $progress['completed']) {
          $completed_courses = $progress_entity->get('completed_courses')->getValue();
          $completed_courses[] = ['target_id' => $id];
          $progress_entity->set('completed_courses', $completed_courses);
          $progress_entity->save();
        }
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error($e->getMessage());
    }
  }

  /**
   * Gets or creates a progress entity for a user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The progress entity or null if not available.
   */
  protected function getOrCreateProgressEntity(UserInterface $user) {
    try {
      if (!$user->hasField('dh_certificate_progress')) {
        return NULL;
      }
      
      $progress = $user->get('dh_certificate_progress')->entity;
      if (!$progress) {
        $progress = $this->entityTypeManager->getStorage('dh_certificate_progress')->create([
          'uid' => $user->id(),
        ]);
        $progress->save();
        $user->set('dh_certificate_progress', $progress);
        $user->save();
      }
      
      return $progress;
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error($e->getMessage());
      return NULL;
    }
  }

  /**
   * Gets progress for all active courses.
   *
   * @return array
   *   Array of course progress data.
   */
  public function getAllCourseProgress() {
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->condition('status', 1)
      ->condition('field_course_meets_what_requirem', NULL, 'IS NOT NULL')  // Only get courses with requirements
      ->sort('field_semester', 'DESC')
      ->sort('field_course_mnemonic', 'ASC')
      ->accessCheck(TRUE); // Add explicit access check
    $course_ids = $query->execute();
    
    $courses = [];
    if (!empty($course_ids)) {
      $course_nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($course_ids);
      foreach ($course_nodes as $node) {
        $courses[] = [
          'nid' => $node->id(),  // Add explicit node ID
          'id' => $node->id(),   // Keep existing id for backward compatibility
          'title' => $node->label(),
          'mnemonic' => $node->hasField('field_course_mnemonic') ? 
            $node->get('field_course_mnemonic')->value : '',
          'description' => $node->hasField('field_short_text_description') ? 
            $node->get('field_short_text_description')->value : '',
        ];
      }
    }
    
    return $courses;
  }

  /**
   * Mark a course as completed for a user.
   *
   * @param int $course_id
   *   The course node ID.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   */
  public function completeCourse($course_id, AccountInterface $account) {
    $progress = [
      'completed' => TRUE,
      'timestamp' => \Drupal::time()->getRequestTime(),
      'progress' => 100,
    ];
    $this->updateProgress($course_id, $account, $progress);
  }

  /**
   * Gets the student progress.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The student progress entity or null if not available.
   */
  public function getStudentProgress(AccountInterface $account) {
    $progress = $this->entityTypeManager
      ->getStorage('student_progress')
      ->loadByProperties(['uid' => $account->id()]);
    
    if (empty($progress)) {
      // Create new progress tracking for this student
      return $this->initializeStudentProgress($account);
    }
    
    return reset($progress);
  }

  /**
   * Updates the requirement progress.
   *
   * @param \Drupal\Core\Entity\EntityInterface $student_progress
   *   The student progress entity.
   * @param int $requirement_id
   *   The requirement ID.
   * @param array $data
   *   The progress data.
   *
   * @throws \InvalidArgumentException
   *   Thrown when the requirement ID or progress data is invalid.
   */
  public function updateRequirementProgress($student_progress, $requirement_id, array $data) {
    $requirement_set = $student_progress->get('requirement_set')->entity;
    $requirements = $requirement_set->get('requirements');
    
    if (!isset($requirements[$requirement_id])) {
      throw new \InvalidArgumentException("Invalid requirement ID: $requirement_id");
    }
    
    $requirement = $requirements[$requirement_id];
    $type = $this->requirementTypeManager->getRequirementType($requirement['type']);
    
    if (!$type->validateProgress($data)) {
      throw new \InvalidArgumentException("Invalid progress data for requirement type: {$requirement['type']}");
    }
    
    $progress_data = $student_progress->get('progress_data')->getValue();
    $progress_data[$requirement_id] = $data;
    $student_progress->set('progress_data', $progress_data);
    $student_progress->save();
  }

  /**
   * Gets progress data for all users.
   *
   * @return array
   *   Array of progress data for all users.
   */
  public function getAllProgress() {
    try {
      // Verify table exists first
      if (!$this->database->schema()->tableExists('course_enrollment')) {
        throw new \Exception('Course enrollment table does not exist');
      }

      $query = $this->database->select('course_enrollment', 'ce');
      $query->join('users_field_data', 'u', 'ce.uid = u.uid');
      $query->join('node_field_data', 'n', 'ce.course_id = n.nid');
      $query->fields('ce', ['uid', 'status', 'completed_date'])
            ->fields('u', ['name'])
            ->fields('n', ['title', 'nid']);

      return $query->execute()->fetchAll();
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error('Failed to get all progress: @error', [
        '@error' => $e->getMessage(),
      ]);
      return [];
    }
  }

  /**
   * Gets all reports data.
   *
   * @return array
   *   Array of reports data.
   */
  public function getReports() {
    return [
      'summary' => $this->getProgressSummary(),
      'details' => $this->getAllProgress(),
    ];
  }

  /**
   * Gets all enrollment data.
   *
   * @return array
   *   Array of enrollment data.
   */
  public function getAllEnrollments() {
    $database = \Drupal::database();
    
    $query = $database->select('course_enrollment', 'ce');
    $query->join('users_field_data', 'u', 'ce.uid = u.uid');
    $query->join('node_field_data', 'n', 'ce.course_id = n.nid');
    $query->fields('ce')
          ->fields('u', ['name'])
          ->fields('n', ['title']);
    
    return $query->execute()->fetchAll();
  }

  /**
   * Gets a summary of overall progress.
   *
   * @return array
   *   Array of summary statistics.
   */
  protected function getProgressSummary() {
    $database = \Drupal::database();
    
    // Get total users with enrollments
    $total_users = $database->select('course_enrollment', 'ce')
      ->fields('ce', ['uid'])
      ->distinct()
      ->countQuery()
      ->execute()
      ->fetchField();
    
    // Get completed courses count
    $completed_courses = $database->select('course_enrollment', 'ce')
      ->condition('status', 'completed')
      ->countQuery()
      ->execute()
      ->fetchField();
    
    return [
      'total_users' => $total_users,
      'completed_courses' => $completed_courses,
    ];
  }

  /**
   * Gets the number of pending actions for a user.
   *
   * @param int $uid
   *   The user ID.
   *
   * @return int
   *   The number of pending actions.
   */
  protected function getPendingActionsCount($uid) {
    $database = \Drupal::database();
    
    return (int) $database->select('course_enrollment', 'ce')
      ->condition('uid', $uid)
      ->condition('status', ['pending', 'in-progress'], 'IN')
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  protected function getUserCourses($uid) {
    $database = \Drupal::database();
    
    // Improved query to get all enrolled courses
    $query = $database->select('course_enrollment', 'ce');
    $query->leftJoin('node_field_data', 'n', 'ce.course_id = n.nid');
    $query->leftJoin('node__field_credits', 'fc', 'n.nid = fc.entity_id');
    $query->leftJoin('node__field_course_mnemonic', 'fmn', 'n.nid = fmn.entity_id');
    
    $query->fields('ce', ['id', 'status', 'completed_date', 'course_id'])
          ->fields('n', ['title'])
          ->fields('fc', ['field_credits_value'])
          ->fields('fmn', ['field_course_mnemonic_value'])
          ->condition('ce.uid', $uid)
          ->condition('n.status', 1) // Only published courses
          ->orderBy('n.title');

    $results = $query->execute()->fetchAll();
    
    // Debug logging
    \Drupal::logger('dh_certificate')->debug('Found @count courses for user @uid', [
      '@count' => count($results),
      '@uid' => $uid
    ]);

    $courses = [];
    foreach ($results as $result) {
      $courses[] = [
        'id' => $result->course_id,
        'title' => $result->title,
        'mnemonic' => $result->field_course_mnemonic_value ?? '',
        'credits' => (int)($result->field_credits_value ?? 0),
        'status' => $result->status,
        'completed_date' => $result->completed_date,
      ];
    }

    return $courses;
  }

  /**
   * Returns the total number of students enrolled in certificate courses.
   */
  public function getTotalStudents() {
    try {
      $count = $this->database->select('course_enrollment', 'e')  // Changed from dh_certificate_enrollments
        ->fields('e', ['uid'])
        ->distinct()
        ->countQuery()
        ->execute()
        ->fetchField();
      
      return (int) $count;
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error('Failed to count students: @error', [
        '@error' => $e->getMessage(),
      ]);
      return 0;
    }
  }

  /**
   * Returns the number of currently active courses.
   */
  public function getActiveCourses() {
    try {
      return (int) $this->database->select('node_field_data', 'n')
        ->condition('n.type', 'course')
        ->condition('n.status', 1)
        ->countQuery()
        ->execute()
        ->fetchField() ?: 0;
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error('Failed to count active courses: @error', [
        '@error' => $e->getMessage(),
      ]);
      return 0;
    }
  }

  /**
   * Returns the total number of completed courses across all students.
   */
  public function getCompletedCoursesCount() {
    try {
      return (int) $this->database->select('course_enrollment', 'e')
        ->condition('e.status', 'completed')
        ->countQuery()
        ->execute()
        ->fetchField() ?: 0;
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error('Failed to count completed courses: @error', [
        '@error' => $e->getMessage(),
      ]);
      return 0;
    }
  }

  /**
   * Gets the core courses required for the certificate.
   *
   * @return array
   *   Array of core course data.
   */
  public function getCoreCourses() {
    try {
      // Check if field_course_type exists
      $field_definitions = \Drupal::service('entity_field.manager')
        ->getFieldDefinitions('node', 'course');
      
      $query = $this->entityTypeManager->getStorage('node')->getQuery()
        ->condition('type', 'course')
        ->condition('status', 1)
        ->accessCheck(TRUE);

      // Use appropriate field based on what's available
      if (isset($field_definitions['field_course_type'])) {
        $query->condition('field_course_type', 'core');
      }
      elseif (isset($field_definitions['field_course_meets_requirement'])) {
        $query->condition('field_course_meets_requirement', 'core');
      }
      elseif (isset($field_definitions['field_required_course'])) {
        $query->condition('field_required_course', 1);
      }
      else {
        // Log warning about missing fields
        \Drupal::logger('dh_certificate')->warning('No core course identification fields found');
        // Fall back to configuration
        $config = \Drupal::config('dh_certificate.requirements');
        return $config->get('core_courses') ?? [];
      }
      
      if (isset($field_definitions['field_course_mnemonic'])) {
        $query->sort('field_course_mnemonic', 'ASC');
      }
      else {
        $query->sort('title', 'ASC');
      }
      
      $nids = $query->execute();
      if (empty($nids)) {
        return [];
      }

      $courses = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
      $core_courses = [];

      foreach ($courses as $course) {
        $core_courses[$course->id()] = [
          'id' => $course->id(),
          'title' => $course->label(),
          'mnemonic' => $this->getFieldValue($course, 'field_course_mnemonic'),
          'credits' => (int)$this->getFieldValue($course, 'field_credits', 0),
          'description' => $this->getFieldValue($course, 'field_short_text_description'),
        ];
      }

      return $core_courses;
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error('Error getting core courses: @message', [
        '@message' => $e->getMessage(),
      ]);
      return [];
    }
  }

  /**
   * Helper function to safely get field values.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to get the field value from.
   * @param string $field_name
   *   The name of the field.
   * @param mixed $default
   *   The default value to return if the field doesn't exist or is empty.
   *
   * @return mixed
   *   The field value or default.
   */
  protected function getFieldValue($entity, $field_name, $default = '') {
    if ($entity->hasField($field_name) && !$entity->get($field_name)->isEmpty()) {
      return $entity->get($field_name)->value;
    }
    return $default;
  }

  /**
   * Gets the required amount of elective credits.
   *
   * @return int
   *   The number of required elective credits.
   */
  public function getRequiredElectiveCredits() {
    return (int)$this->state->get('dh_certificate.elective_credits', 12);
  }

  /**
   * Gets the certificate completion deadline.
   *
   * @return string|null
   *   The formatted completion deadline or null if not set.
   */
  public function getCompletionDeadline() {
    $deadline = $this->state->get('dh_certificate.completion_deadline');
    return $deadline ? date('Y-m-d', $deadline) : NULL;
  }
}
