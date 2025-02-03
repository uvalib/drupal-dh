<?php

namespace Drupal\dh_certificate\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Drush commands for DH Certificate.
 */
class DHCertificateCommands extends DrushCommands {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new DHCertificateCommands object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(Connection $database, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct();
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Generate random course enrollments for a user.
   *
   * @param string $user_ids
   *   Comma-separated list of user IDs.
   * @param array $options
   *   An associative array of options.
   *
   * @command dh-certificate:generate-enrollments
   * @aliases dhc-gen-enroll
   * @option count Number of enrollments to generate
   * @option retain Retain existing enrollments
   */
  public function generateEnrollments($user_ids, array $options = ['count' => 5, 'retain' => FALSE]) {
    $user_ids_array = array_map('trim', explode(',', $user_ids));
    $user_storage = $this->entityTypeManager->getStorage('user');
    $course_storage = $this->entityTypeManager->getStorage('node');

    // Get all courses
    $course_query = $course_storage->getQuery()
      ->condition('type', 'course')
      ->accessCheck(FALSE);
    $course_ids = $course_query->execute();
    $courses = $course_storage->loadMultiple($course_ids);

    $statuses = ['pending', 'in-progress', 'completed'];

    foreach ($user_ids_array as $user_id) {
      $user = $user_storage->load($user_id);
      if ($user) {
        // Clear existing enrollments if --retain flag is not passed
        if (!$options['retain']) {
          $this->database->delete('course_enrollment')
            ->condition('uid', $user_id)
            ->execute();
        }

        // Limit the number of enrollments to 5-8
        $num_enrollments = rand(5, 8);
        $selected_courses = array_rand($courses, $num_enrollments);

        foreach ($selected_courses as $course_index) {
          $course = $courses[$course_index];
          $status = $statuses[array_rand($statuses)];
          $completed_date = $status === 'completed' ? time() : NULL;

          $this->database->insert('course_enrollment')
            ->fields([
              'uid' => $user_id,
              'course_id' => $course->id(),
              'status' => $status,
              'completed_date' => $completed_date,
            ])
            ->execute();
        }
        $this->logger()->success(dt('Enrollments generated for user ID @uid', ['@uid' => $user_id]));
      }
      else {
        $this->logger()->error(dt('User ID @uid not found', ['@uid' => $user_id]));
      }
    }

    // List all enrollments for the specified users
    foreach ($user_ids_array as $user_id) {
      $enrollments = $this->database->select('course_enrollment', 'ce')
        ->fields('ce', ['course_id', 'status'])
        ->condition('uid', $user_id)
        ->execute()
        ->fetchAll();

      $user = $user_storage->load($user_id);
      foreach ($enrollments as $enrollment) {
        $course = $course_storage->load($enrollment->course_id);
        if (!$course || !$user) {
          continue;
        }
        $this->logger()->notice(sprintf(
          "%s is %s in %s",
          $user->getDisplayName(),
          $enrollment->status,
          $course->label()
        ));
      }
    }

    // Debugging to stderr
    fwrite(STDERR, "Debug: Finished generating and listing enrollments.\n");
  }

  /**
   * The progress manager.
   *
   * @var \Drupal\dh_certificate\ProgressManagerInterface
   */
  protected $progressManager;

  /**
   * The requirement type manager.
   *
   * @var \Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface
   */
  protected $requirementTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('entity_type.manager'),
      $container->get('dh_certificate.progress_manager'),
      $container->get('plugin.manager.requirement_type')
    );
  }

  /**
   * Generate test data for certificate requirements.
   *
   * @command dh-certificate:generate-test
   * @aliases dhc-gen-test
   * @option reset Delete existing test data before generating new data
   * @option uid User ID to create enrollments for (defaults to 12)
   */
  public function generateTestData($options = ['reset' => FALSE, 'uid' => 12]) {
    // Validate user exists
    $user = $this->entityTypeManager->getStorage('user')->load($options['uid']);
    if (!$user) {
      throw new \Exception(sprintf('User %d not found', $options['uid']));
    }

    if ($options['reset']) {
      $this->deleteExistingTestData();
    }

    // Create core courses with various completion statuses
    $core_courses = $this->createTestCourses([
      'Introduction to Fake Humanities in Fake Digital Environments' => [
        'type' => 'core',
        'credits' => 3,
        'term' => 'Fall 2023',
        'status' => 'complete'
      ],
      'Fake Methods in Fake Research' => [
        'type' => 'core',
        'credits' => 3,
        'term' => 'Spring 2024',
        'status' => 'complete'
      ],
      'Fake Digital Archives and Fake Preservation' => [
        'type' => 'core',
        'credits' => 3,
        'term' => 'Fall 2023',
        'status' => 'in_progress'
      ],
      'Fake Data Visualization for Fake Humanists' => [
        'type' => 'core',
        'credits' => 3,
        'term' => 'Spring 2024',
        'status' => 'not_started'
      ],
      'Advanced Fake Theory in Fake Digital Humanities' => [
        'type' => 'core',
        'credits' => 3,
        'term' => 'Fall 2024',
        'status' => 'not_started'
      ],
    ], $options['uid']);

    // Create elective courses with various completion statuses
    $elective_courses = $this->createTestCourses([
      'Advanced Fake Methods in Fake Digitization' => [
        'type' => 'elective',
        'credits' => 3,
        'term' => 'Spring 2024',
        'status' => 'in_progress'
      ],
      'Fake Digital Humanities Fake Project Management' => [
        'type' => 'elective',
        'credits' => 3,
        'term' => 'Fall 2023',
        'status' => 'not_started'
      ],
      'Fake Text Analysis with Fake Tools' => [
        'type' => 'elective',
        'credits' => 3,
        'term' => 'Spring 2024',
        'status' => 'complete'
      ],
      'Fake Network Analysis for Fake Humanities' => [
        'type' => 'elective',
        'credits' => 3,
        'term' => 'Fall 2024',
        'status' => 'not_started'
      ],
    ], $options['uid']);

    // Update certificate requirements configuration
    $config = \Drupal::configFactory()->getEditable('dh_certificate.requirements');
    $config
      ->set('core_courses', array_keys($core_courses))
      ->set('elective_courses', array_keys($elective_courses))
      ->set('elective_credits', 6)
      ->set('due_date', [
        'type' => 'academic',
        'value' => 'Spring-2024',
        'format' => 'term-year',
      ])
      ->save();

    $this->output()->writeln(dt('Generated @core core courses and @elective elective courses', [
      '@core' => count($core_courses),
      '@elective' => count($elective_courses),
    ]));
  }

  /**
   * Creates test courses with the given configurations.
   */
  protected function createTestCourses(array $courses, $uid) {
    $created = [];
    $counter = 1000; // Start counter for unique mnemonics
    
    foreach ($courses as $title => $config) {
      try {
        // Check if course already exists
        $existing = $this->entityTypeManager->getStorage('node')
          ->loadByProperties([
            'type' => 'course',
            'title' => $title,
          ]);
        
        if (!empty($existing)) {
          $node = reset($existing);
          $this->output()->writeln(dt('Course exists: @title', [
            '@title' => $title,
          ]));
        }
        else {
          // Create a new course node
          $mnemonic = 'FAKE-' . $counter++;
          $node = $this->entityTypeManager->getStorage('node')->create([
            'type' => 'course',
            'title' => $title,
            'field_course_type' => $config['type'],
            'field_credits' => $config['credits'],
            'field_term' => $config['term'],
            'field_status' => $config['status'],
            'field_course_mnemonic' => $mnemonic,
            'status' => 1, // Published
          ]);
          
          $node->save();
          $this->output()->writeln(dt('Created course: @title with mnemonic @mnemonic', [
            '@title' => $title,
            '@mnemonic' => $mnemonic,
          ]));
        }
        
        $created[$node->id()] = [
          'title' => $title,
          'mnemonic' => $mnemonic,
          'status' => $config['status'],
        ];
        
        // Check for existing enrollment
        $enrollment_exists = $this->checkEnrollmentExists($uid, $node->id());
        if (!$enrollment_exists) {
          // Create enrollment for the specified user
          $this->createTestEnrollment($uid, $node->id(), $config['status']);
        }
        else {
          $this->output()->writeln(dt('Enrollment exists for user @uid in course @title', [
            '@uid' => $uid,
            '@title' => $title,
          ]));
        }
      }
      catch (\Exception $e) {
        $this->logger()->error($e->getMessage());
        $this->logger()->error($e->getTraceAsString());
      }
    }
    return $created;
  }

  /**
   * Check if an enrollment already exists.
   */
  protected function checkEnrollmentExists($uid, $course_id) {
    $database = \Drupal::database();
    return (bool) $database->select('course_enrollment', 'ce')
      ->condition('uid', $uid)
      ->condition('course_id', $course_id)
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  /**
   * Creates a test enrollment for a user in a course.
   */
  protected function createTestEnrollment($uid, $course_id, $status) {
    $status_map = [
      'complete' => 'completed',
      'in_progress' => 'in-progress',
      'not_started' => 'pending'
    ];

    try {
      $database = \Drupal::database();
      
      $database->insert('course_enrollment')
        ->fields([
          'uid' => $uid,
          'course_id' => $course_id,
          'status' => $status_map[$status] ?? 'pending',
          'completed_date' => $status === 'complete' ? time() : NULL,
        ])
        ->execute();
        
      $this->output()->writeln(dt('Created enrollment for user @uid in course @cid with status @status', [
        '@uid' => $uid,
        '@cid' => $course_id,
        '@status' => $status_map[$status] ?? 'pending',
      ]));
    }
    catch (\Exception $e) {
      $this->logger()->error($e->getMessage());
    }
  }

  /**
   * Deletes existing test data.
   */
  protected function deleteExistingTestData() {
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->condition('field_course_mnemonic', 'FAKE-%', 'LIKE')
      ->accessCheck(FALSE);  // Explicitly disable access checking for admin operation

    $nids = $query->execute();
    
    if (!empty($nids)) {
      $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
      $this->entityTypeManager->getStorage('node')->delete($nodes);
      $this->output()->writeln(dt('Deleted @count existing test courses', [
        '@count' => count($nids),
      ]));
    }
  }

  /**
   * Check certificate progress for a user.
   *
   * @command dh-certificate:check-progress
   * @aliases dhc-progress
   * @usage dh-certificate:check-progress 1
   */
  public function checkProgress($uid) {
    // First check if we have the necessary setup
    $status = $this->checkCertificateSetup();
    if ($status !== TRUE) {
      $this->output()->writeln("<error>$status</error>");
      return 1;
    }

    try {
      // Check if the user exists
      $user = $this->entityTypeManager->getStorage('user')->load($uid);
      if (!$user) {
        $this->output()->writeln("<error>User not found: $uid</error>");
        return 1;
      }

      // Check for enrollments
      $database = \Drupal::database();
      $enrollment_count = $database->select('course_enrollment', 'ce')
        ->condition('uid', $uid)
        ->countQuery()
        ->execute()
        ->fetchField();

      if (!$enrollment_count) {
        $this->output()->writeln("\n=== Certificate Progress for User $uid ===");
        $this->output()->writeln("Status: No course enrollments found");
        $this->output()->writeln("\nTo enroll in courses, try:");
        $this->output()->writeln("  drush dhc-gen --reset");
        return 0;
      }

      // Get and display progress
      $progress = $this->progressManager->getUserProgress($user);
      
      $this->output()->writeln("\n=== Certificate Progress for User $uid ===");
      $this->output()->writeln(sprintf(
        "Overall Status: %d/%d courses (%d%% complete)",
        $progress['completed_courses'],
        $progress['total_courses'],
        $progress['total_percentage']
      ));

      if (!empty($progress['courses'])) {
        $this->output()->writeln("\nCourse Details:");
        $this->output()->writeln(str_repeat('-', 70));
        foreach ($progress['courses'] as $course) {
          $credits = isset($course['credits']) ? sprintf("%d credits", $course['credits']) : '';
          $type = isset($course['type']) ? sprintf("[%s]", $course['type']) : '';
          
          $this->output()->writeln(sprintf(
            "%-40s | %-10s %s %s",
            substr($course['title'], 0, 40),
            $course['status'],
            $type,
            $credits
          ));
        }
        $this->output()->writeln(str_repeat('-', 70));
      }
      
      return 0;
    }
    catch (\Exception $e) {
      $this->logger()->error($e->getMessage());
      $this->output()->writeln("<error>" . $e->getMessage() . "</error>");
      return 1;
    }
  }

  /**
   * Checks if certificate system is properly set up.
   */
  protected function checkCertificateSetup() {
    // Check if course content type exists
    if (!$this->entityTypeManager->getStorage('node_type')->load('course')) {
      return 'Course content type does not exist. Please reinstall the module.';
    }

    // Check if enrollment table exists
    $database = \Drupal::database();
    if (!$database->schema()->tableExists('course_enrollment')) {
      return 'Course enrollment table does not exist. Please reinstall the module.';
    }

    return TRUE;
  }

  /**
   * Clean up all certificate progress entities.
   *
   * @command dh-certificate:cleanup-progress
   * @aliases dhc-clean-progress
   * @usage dh-certificate:cleanup-progress
   */
  public function cleanupProgress() {
    try {
      // First try entity API approach
      $storage = $this->entityTypeManager->getStorage('dh_certificate_progress');
      $query = $storage->getQuery()
        ->accessCheck(FALSE);
      $ids = $query->execute();

      if (!empty($ids)) {
        $entities = $storage->loadMultiple($ids);
        $storage->delete($entities);
        $this->output()->writeln(dt('Deleted @count certificate progress entities.', [
          '@count' => count($ids),
        ]));
      }

      // Fallback to direct database cleanup
      $database = \Drupal::database();
      if ($database->schema()->tableExists('dh_certificate_progress')) {
        $database->truncate('dh_certificate_progress')->execute();
        $this->output()->writeln(dt('Cleaned up certificate progress table.'));
      }

      // Also clean up any user references
      $user_storage = $this->entityTypeManager->getStorage('user');
      $users = $user_storage->loadMultiple();
      foreach ($users as $user) {
        if ($user->hasField('dh_certificate_progress')) {
          $user->set('dh_certificate_progress', NULL);
          $user->save();
        }
      }
      $this->output()->writeln(dt('Cleaned up user certificate progress references.'));

    } catch (\Exception $e) {
      $this->logger()->error($e->getMessage());
      throw new \Exception('Failed to clean up certificate progress entities: ' . $e->getMessage());
    }
  }

  /**
   * Clean up all course enrollment data.
   *
   * @command dh-certificate:cleanup-enrollments
   * @aliases dhc-clean-enroll
   * @usage dh-certificate:cleanup-enrollments
   */
  public function cleanupEnrollments() {
    try {
      // Direct database cleanup for reliability
      $database = \Drupal::database();
      if ($database->schema()->tableExists('course_enrollment')) {
        $count = $database->select('course_enrollment', 'ce')
          ->countQuery()
          ->execute()
          ->fetchField();
          
        $database->truncate('course_enrollment')->execute();
        $this->output()->writeln(dt('Cleaned up @count course enrollment records.', [
          '@count' => $count,
        ]));
      } else {
        $this->output()->writeln(dt('No course enrollment table found.'));
      }
    } catch (\Exception $e) {
      $this->logger()->error($e->getMessage());
      throw new \Exception('Failed to clean up course enrollments: ' . $e->getMessage());
    }
  }

  /**
   * Debug enrollment data.
   *
   * @command dh-certificate:debug-enrollments
   * @aliases dhc-debug
   */
  public function debugEnrollments() {
    $database = \Drupal::database();
    
    $this->output()->writeln("\n=== Course Enrollment Debug ===");

    // Check existence and count
    $exists = $database->schema()->tableExists('course_enrollment');
    $count = 0;
    if ($exists) {
      $count = $database->select('course_enrollment', 'ce')
        ->countQuery()
        ->execute()
        ->fetchField();
    }
    
    $this->output()->writeln("Table exists: " . ($exists ? 'Yes' : 'No'));
    $this->output()->writeln("Total enrollments: $count");

    // Only show data if we have enrollments
    if ($count > 0) {
      // Get all enrollments with course titles
      $query = $database->select('course_enrollment', 'ce');
      $query->join('node_field_data', 'n', 'ce.course_id = n.nid');
      $query->fields('ce', ['id', 'uid', 'status', 'completed_date'])
        ->fields('n', ['title'])
        ->orderBy('ce.id', 'ASC');
      
      $results = $query->execute()->fetchAll();

      $this->output()->writeln("\nAll enrollments:");
      $this->output()->writeln(str_repeat('-', 80));
      $this->output()->writeln(sprintf(
        "%-4s | %-6s | %-40s | %-10s | %s",
        "ID", "User", "Course", "Status", "Completed"
      ));
      $this->output()->writeln(str_repeat('-', 80));
      
      foreach ($results as $row) {
        $completed = $row->completed_date ? date('Y-m-d', $row->completed_date) : 'N/A';
        $this->output()->writeln(sprintf(
          "%-4d | %-6d | %-40s | %-10s | %s",
          $row->id,
          $row->uid,
          substr($row->title, 0, 40),
          $row->status,
          $completed
        ));
      }
      $this->output()->writeln(str_repeat('-', 80));
    }
    
    $this->output()->writeln("\n=== End Debug ===\n");
  }

  /**
   * @command dh-certificate:setup-requirements
   */
  public function setupRequirements() {
    $requirements = [
      'core_courses' => [
        'type' => 'course',
        'label' => 'Core Courses',
        'required' => TRUE,
        'workflow' => 'course_workflow',
        'courses' => [
          // Course references
        ],
      ],
      'tool_proficiency' => [
        'type' => 'task',
        'label' => 'Tool Proficiency',
        'required' => TRUE,
        'workflow' => 'advisor_approval',
        'options' => [
          'tools' => ['git', 'python', 'r'],
        ],
      ],
      // Add other requirements...
    ];

    // Create requirement set
    $requirement_set = RequirementSet::create([
      'id' => 'default',
      'label' => 'Default Requirements',
      'requirements' => $requirements,
    ]);
    $requirement_set->save();
  }

  /**
   * Check dashboard progress for a user.
   *
   * @command dh:check-dashboard
   * @aliases dh-cp
   * @usage dh:check-dashboard 1
   *   Check dashboard progress for user 1.
   */
  public function checkDashboardProgress($uid) {
    try {
        $user = $this->entityTypeManager->getStorage('user')->load($uid);
        
        if (!$user) {
            throw new \Exception(dt('User @uid not found.', ['@uid' => $uid]));
        }

        if (\Drupal::moduleHandler()->moduleExists('dh_dashboard')) {
            $progress_service = \Drupal::service('dh_dashboard.progress');
            $progress = $progress_service->getUserProgress();
            
            $this->output()->writeln(dt('Dashboard progress for user @name:', ['@name' => $user->getDisplayName()]));
            $this->output()->writeln(json_encode($progress, JSON_PRETTY_PRINT));
        } else {
            $this->output()->writeln('DH Dashboard module is not enabled.');
        }
    } catch (\Exception $e) {
        $this->logger()->error($e->getMessage());
        throw $e;
    }
  }

  /**
   * Reset all DH data.
   *
   * @command dh:reset-all
   * @aliases dh-reset
   * @usage dh:reset-all
   *   Reset all DH related data.
   */
  public function resetAll() {
    $this->output()->writeln('Resetting DH data...');
    
    // Clean up certificate data
    $this->cleanupProgress();
    $this->cleanupEnrollments();
    
    // Reset dashboard if available
    if (\Drupal::moduleHandler()->moduleExists('dh_dashboard')) {
        $this->output()->writeln('Resetting dashboard data...');
        // Add dashboard reset logic here
    }
    
    $this->output()->writeln('Reset complete.');
  }

  /**
   * List all enrollments.
   *
   * @command dh-certificate:list-enrollments
   * @aliases dhc-list-enroll
   */
  public function listEnrollments() {
    $database = \Drupal::database();
    $query = $database->select('course_enrollment', 'ce')
      ->fields('ce', ['uid', 'course_id', 'status']);
    
    $enrollments = $query->execute()->fetchAll();

    if (empty($enrollments)) {
      $this->output()->writeln('No enrollments found.');
      return;
    }

    $course_storage = $this->entityTypeManager->getStorage('node');
    $user_storage = $this->entityTypeManager->getStorage('user');

    foreach ($enrollments as $enrollment) {
      $user = $user_storage->load($enrollment->uid);
      $course = $course_storage->load($enrollment->course_id);
      
      if (!$user || !$course) {
        continue;
      }

      $this->output()->writeln(sprintf(
        "%-15s | %-40s | %s",
        $user->getAccountName(),
        $course->label(),
        $enrollment->status
      ));
    }
  }

  /**
   * Generate example requirement sets.
   *
   * @command dh-certificate:generate-requirements
   * @aliases dhc-gen-req
   * @option reset Delete existing requirement sets before generating new ones
   */
  public function generateRequirementSets(array $options = ['reset' => FALSE]) {
    if ($options['reset']) {
      $storage = $this->entityTypeManager->getStorage('requirement_set');
      $ids = $storage->getQuery()
        ->accessCheck(FALSE)
        ->execute();
      if (!empty($ids)) {
        $storage->delete($storage->loadMultiple($ids));
        $this->output()->writeln('Deleted existing requirement sets.');
      }
    }

    // Create example requirement sets
    $sets = [
      'certificate_standard' => [
        'label' => 'Digital Humanities Certificate',
        'requirements' => [
          'core_courses' => [
            'type' => 'course',
            'label' => 'Core Courses',
            'required' => TRUE,
            'config' => [
              'minimum_credits' => 15,
              'course_type' => 'core'
            ]
          ],
          'elective_courses' => [
            'type' => 'course',
            'label' => 'Elective Courses',
            'required' => TRUE,
            'config' => [
              'minimum_credits' => 6,
              'course_type' => 'elective'
            ]
          ],
          'tool_proficiency' => [
            'type' => 'task',
            'label' => 'Tool Proficiency',
            'required' => TRUE,
            'config' => [
              'tools' => [
                'git' => 'Git Version Control',
                'python' => 'Python Programming',
                'r' => 'R Statistical Computing'
              ]
            ]
          ],
          'capstone_project' => [
            'type' => 'project',
            'label' => 'Capstone Project',
            'required' => TRUE,
            'config' => [
              'milestones' => [
                'proposal' => 'Project Proposal',
                'implementation' => 'Project Implementation',
                'presentation' => 'Final Presentation'
              ]
            ]
          ]
        ]
      ],
      'advanced_certificate' => [
        'label' => 'Advanced DH Certificate',
        'requirements' => [
          'core_courses' => [
            'type' => 'course',
            'label' => 'Advanced Core Courses',
            'required' => TRUE,
            'config' => [
              'minimum_credits' => 18,
              'course_type' => 'advanced_core'
            ]
          ],
          'research_project' => [
            'type' => 'project',
            'label' => 'Research Project',
            'required' => TRUE,
            'config' => [
              'milestones' => [
                'proposal' => 'Research Proposal',
                'methodology' => 'Methods Development',
                'implementation' => 'Project Implementation',
                'paper' => 'Research Paper',
                'defense' => 'Project Defense'
              ]
            ]
          ]
        ]
      ]
    ];

    foreach ($sets as $id => $data) {
      try {
        $requirement_set = $this->entityTypeManager->getStorage('requirement_set')->create([
          'id' => $id,
          'label' => $data['label'],
          'requirements' => $data['requirements'],
          'status' => TRUE
        ]);
        $requirement_set->save();
        
        $this->output()->writeln(sprintf(
          'Created requirement set: %s [%s]',
          $data['label'],
          $id
        ));
      }
      catch (\Exception $e) {
        $this->logger()->error($e->getMessage());
      }
    }
  }

}
