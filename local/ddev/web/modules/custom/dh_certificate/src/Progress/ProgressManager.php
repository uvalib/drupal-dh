<?php

namespace Drupal\dh_certificate\Progress;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\dh_certificate\Progress\ProgressManagerInterface;

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
   * Constructs a new ProgressManager.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    StateInterface $state
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserProgress(?AccountInterface $account = NULL) {
    $enrollment_storage = $this->entityTypeManager->getStorage('course_enrollment');
    $uid = $account ? $account->id() : \Drupal::currentUser()->id();

    // Get enrollments
    $query = $enrollment_storage->getQuery()
      ->condition('uid', $uid)
      ->accessCheck(FALSE)
      ->execute();
    
    if (empty($query)) {
      return NULL;
    }

    $enrollments = $enrollment_storage->loadMultiple($query);
    
    // Calculate progress metrics
    $total_courses = count($enrollments);
    $completed_courses = 0;
    $courses = [];

    foreach ($enrollments as $enrollment) {
      $course = $enrollment->get('course_id')->entity;
      if (!$course) {
        continue;
      }

      if ($enrollment->get('status')->value === 'completed') {
        $completed_courses++;
      }

      $courses[] = [
        'title' => $course->label(),
        'status' => $enrollment->get('status')->value,
        'mnemonic' => $course->get('field_course_code')->value,
      ];
    }

    return [
      'total_courses' => $total_courses,
      'completed_courses' => $completed_courses,
      'total_percentage' => $total_courses > 0 ? round(($completed_courses / $total_courses) * 100) : 0,
      'pending_actions' => $total_courses - $completed_courses,
      'courses' => $courses,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getAllProgress() {
    $user_storage = $this->entityTypeManager->getStorage('user');
    $query = $user_storage->getQuery()
      ->condition('status', 1)
      ->accessCheck(FALSE)
      ->execute();
    
    $progress = [];
    foreach ($user_storage->loadMultiple($query) as $account) {
      $progress[$account->id()] = $this->getUserProgress($account);
    }
    
    return $progress;
  }

  /**
   * {@inheritdoc}
   */
  public function getTotalStudents() {
    $enrollment_storage = $this->entityTypeManager->getStorage('course_enrollment');
    $query = $enrollment_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', ['in-progress', 'completed'], 'IN')
      ->groupBy('uid')
      ->execute();
    
    return count($query);
  }

  /**
   * {@inheritdoc}
   */
  public function getCompletedCoursesCount() {
    $enrollment_storage = $this->entityTypeManager->getStorage('course_enrollment');
    return $enrollment_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', 'completed')
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getActiveCourses() {
    $course_storage = $this->entityTypeManager->getStorage('node');
    return $course_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'course')
      ->condition('status', 1)
      ->count()
      ->execute();
  }

  /**
   * Gets the list of core courses.
   *
   * @return array
   *   Array of core course data.
   */
  public function getCoreCourses() {
    try {
      $storage = $this->entityTypeManager->getStorage('certificate_course');
      $query = $storage->getQuery()
        ->condition('type', 'core')
        ->accessCheck(FALSE)
        ->execute();
      
      $courses = [];
      if (!empty($query)) {
        foreach ($storage->loadMultiple($query) as $certificate_course) {
          // Load the referenced course node
          $course = $this->entityTypeManager->getStorage('node')
            ->load($certificate_course->get('course_id'));
          
          if ($course) {
            $courses[] = [
              'id' => $course->id(),
              'title' => $course->label(),
              'credits' => $certificate_course->get('credits'),
              'term' => $course->hasField('field_term') ? 
                $course->get('field_term')->value : '',
            ];
          }
        }
      }
      
      return $courses;
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error('Failed to get core courses: @error', [
        '@error' => $e->getMessage()
      ]);
      return [];
    }
  }

  /**
   * Gets the required number of elective credits.
   *
   * @return int
   *   Number of required elective credits.
   */
  public function getRequiredElectiveCredits() {
    return (int) \Drupal::config('dh_certificate.settings')
      ->get('elective_credits') ?? 6;
  }

  /**
   * Gets the completion deadline configuration.
   *
   * @return array
   *   Completion deadline configuration.
   */
  public function getCompletionDeadline() {
    return \Drupal::config('dh_certificate.settings')
      ->get('completion_deadline') ?? [
        'type' => 'academic',
        'value' => 'Spring-2024',
      ];
  }
}
