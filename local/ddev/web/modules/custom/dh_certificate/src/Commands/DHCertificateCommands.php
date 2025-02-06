<?php

namespace Drupal\dh_certificate\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\dh_certificate\Progress\ProgressManagerInterface;
use Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\dh_certificate\Entity\RequirementSet;

/**
 * DH Certificate Drush commands.
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
   * The progress manager.
   *
   * @var \Drupal\dh_certificate\Progress\ProgressManagerInterface
   */
  protected $progressManager;

  /**
   * The requirement type manager.
   *
   * @var \Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface
   */
  protected $requirementTypeManager;

  /**
   * Constructs a new DHCertificateCommands object.
   */
  public function __construct(
    Connection $database,
    EntityTypeManagerInterface $entity_type_manager,
    ProgressManagerInterface $progress_manager,
    RequirementTypeManagerInterface $requirement_type_manager
  ) {
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
    $this->progressManager = $progress_manager;
    $this->requirementTypeManager = $requirement_type_manager;
  }

  /**
   * Helper methods.
   */
  protected function checkCertificateSetup() {
    // Check if course content type exists
    if (!$this->entityTypeManager->getStorage('node_type')->load('course')) {
      return 'Course content type does not exist. Please reinstall the module.';
    }

    // Check if course_enrollment entity type exists
    if (!$this->entityTypeManager->hasDefinition('course_enrollment')) {
      return 'Course enrollment entity type does not exist. Please reinstall the module.';
    }

    return TRUE;
  }

  protected function cleanupProgress() {
    try {
      // Clean up progress entities using Entity API
      if ($this->entityTypeManager->hasDefinition('dh_certificate_progress')) {
        $storage = $this->entityTypeManager->getStorage('dh_certificate_progress');
        $ids = $storage->getQuery()
          ->accessCheck(FALSE)
          ->execute();

        if (!empty($ids)) {
          $entities = $storage->loadMultiple($ids);
          $storage->delete($entities);
          $this->output()->writeln(dt('Cleaned up certificate progress entities.'));
        }
      }

      // Clean up user references using Entity API
      $user_storage = $this->entityTypeManager->getStorage('user');
      $user_query = $user_storage->getQuery()
        ->accessCheck(FALSE)
        ->condition('dh_certificate_progress', NULL, 'IS NOT NULL')
        ->execute();
      
      if (!empty($user_query)) {
        $users = $user_storage->loadMultiple($user_query);
        foreach ($users as $user) {
          $user->set('dh_certificate_progress', NULL);
          $user->save();
        }
        $this->output()->writeln(dt('Cleaned up certificate progress references for @count users.', [
          '@count' => count($users),
        ]));
      }

      return TRUE;
    }
    catch (\Exception $e) {
      $this->logger()->warning($e->getMessage());
      return TRUE;
    }
  }

  protected function cleanupEnrollments() {
    try {
      $storage = $this->entityTypeManager->getStorage('course_enrollment');
      $ids = $storage->getQuery()
        ->accessCheck(FALSE)
        ->execute();
      
      if (!empty($ids)) {
        $entities = $storage->loadMultiple($ids);
        $count = count($entities);
        $storage->delete($entities);
        $this->output()->writeln(dt('Cleaned up @count course enrollment records.', [
          '@count' => $count,
        ]));
      } else {
        $this->output()->writeln(dt('No course enrollment records found.'));
      }
    }
    catch (\Exception $e) {
      $this->logger()->error($e->getMessage());
      throw new \Exception('Failed to clean up course enrollments: ' . $e->getMessage());
    }
  }

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

  protected function checkEnrollmentExists($uid, $course_id) {
    return (bool) $this->entityTypeManager
      ->getStorage('course_enrollment')
      ->loadByProperties([
        'uid' => $uid,
        'course_id' => $course_id,
      ]);
  }

  protected function createTestEnrollment($uid, $course_id, $status) {
    $status_map = [
      'complete' => 'completed',
      'in_progress' => 'in-progress',
      'not_started' => 'pending'
    ];

    try {
      $enrollment = $this->entityTypeManager
        ->getStorage('course_enrollment')
        ->create([
          'uid' => $uid,
          'course_id' => $course_id,
          'status' => $status_map[$status] ?? 'pending',
          'completed_date' => $status === 'complete' ? time() : NULL,
          'enrolled_date' => \Drupal::time()->getRequestTime(),
        ]);
      
      $enrollment->save();
        
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
   * Command methods - Progress and Enrollments
   */
  public function generateEnrollments($user_ids, array $options = ['count' => 5, 'retain' => FALSE]) {
    $user_ids_array = array_map('trim', explode(',', $user_ids));
    $user_storage = $this->entityTypeManager->getStorage('user');
    $course_storage = $this->entityTypeManager->getStorage('node');
    $enrollment_storage = $this->entityTypeManager->getStorage('course_enrollment');

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
          $existing_enrollments = $enrollment_storage->loadByProperties(['uid' => $user_id]);
          if ($existing_enrollments) {
            $enrollment_storage->delete($existing_enrollments);
          }
        }

        // Generate new enrollments
        $num_enrollments = rand(5, 8);
        $selected_courses = array_rand($courses, $num_enrollments);

        foreach ($selected_courses as $course_index) {
          $course = $courses[$course_index];
          $status = $statuses[array_rand($statuses)];
          
          $enrollment = $enrollment_storage->create([
            'uid' => $user_id,
            'course_id' => $course->id(),
            'status' => $status,
            'completed_date' => $status === 'completed' ? time() : NULL,
            'enrolled_date' => \Drupal::time()->getRequestTime(),
          ]);
          $enrollment->save();
        }
        
        $this->logger()->success(dt('Enrollments generated for user ID @uid', ['@uid' => $user_id]));
      }
    }

    // List all enrollments for the specified users
    foreach ($user_ids_array as $user_id) {
      $enrollments = $enrollment_storage->loadByProperties(['uid' => $user_id]);
      $user = $user_storage->load($user_id);
      
      foreach ($enrollments as $enrollment) {
        $course = $course_storage->load($enrollment->get('course_id')->target_id);
        if (!$course || !$user) {
          continue;
        }
        $this->logger()->notice(sprintf(
          "%s is %s in %s",
          $user->getDisplayName(),
          $enrollment->get('status')->value,
          $course->label()
        ));
      }
    }

    // Debugging to stderr
    fwrite(STDERR, "Debug: Finished generating and listing enrollments.\n");
  }

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
      $enrollment_storage = $this->entityTypeManager->getStorage('course_enrollment');
      $enrollment_count = $enrollment_storage->getQuery()
        ->condition('uid', $uid)
        ->count()
        ->execute();

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

  public function debugEnrollments() {
    $storage = $this->entityTypeManager->getStorage('course_enrollment');
    
    $this->output()->writeln("\n=== Course Enrollment Debug ===");

    // Get count of enrollments
    $count = $storage->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
    
    $this->output()->writeln("Total enrollments: $count");

    if ($count > 0) {
      $enrollments = $storage->getQuery()
        ->accessCheck(FALSE)
        ->execute();
      $enrollments = $storage->loadMultiple($enrollments);
      
      $this->output()->writeln("\nAll enrollments:");
      $this->output()->writeln(str_repeat('-', 80));
      $this->output()->writeln(sprintf(
        "%-4s | %-6s | %-40s | %-10s | %s",
        "ID", "User", "Course", "Status", "Completed"
      ));
      $this->output()->writeln(str_repeat('-', 80));
      
      foreach ($enrollments as $enrollment) {
        $user = $this->entityTypeManager->getStorage('user')->load($enrollment->get('uid')->target_id);
        $course = $this->entityTypeManager->getStorage('node')->load($enrollment->get('course_id')->target_id);
        
        if (!$user || !$course) {
          continue;
        }

        $completed = $enrollment->get('completed_date')->value 
          ? date('Y-m-d', $enrollment->get('completed_date')->value) 
          : 'N/A';

        $this->output()->writeln(sprintf(
          "%-4d | %-6d | %-40s | %-10s | %s",
          $enrollment->id(),
          $user->id(),
          substr($course->label(), 0, 40),
          $enrollment->get('status')->value,
          $completed
        ));
      }
      $this->output()->writeln(str_repeat('-', 80));
    }
    
    $this->output()->writeln("\n=== End Debug ===\n");
  }

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
   * Command methods - Templates
   */
  public function listTemplates() {
    $templates = $this->entityTypeManager->getStorage('requirement_type_template')->loadMultiple();
    $rows = [];
    
    foreach ($templates as $template) {
      $rows[] = [
        'id' => $template->id(),
        'label' => $template->label(),
        'type' => $template->getType(),
        'weight' => $template->getWeight(),
      ];
    }
    
    return new RowsOfFields($rows);
  }

  public function createTemplate($id, $label, $type, array $options = ['weight' => 0, 'config' => '{}']) {
    try {
      $storage = $this->entityTypeManager->getStorage('requirement_type_template');
      $template = $storage->create([
        'id' => $id,
        'label' => $label,
        'type' => $type,
        'weight' => $options['weight'],
        'config' => json_decode($options['config'], TRUE) ?: [],
      ]);
      
      $template->save();
      $this->logger()->success(dt('Created template: @label', ['@label' => $label]));
    }
    catch (\Exception $e) {
      $this->logger()->error($e->getMessage());
    }
  }

  public function deleteTemplate($id) {
    try {
      $storage = $this->entityTypeManager->getStorage('requirement_type_template');
      $template = $storage->load($id);
      
      if (!$template) {
        throw new \Exception(dt('Template @id not found.', ['@id' => $id]));
      }
      
      $template->delete();
      $this->logger()->success(dt('Deleted template: @id', ['@id' => $id]));
    }
    catch (\Exception $e) {
      $this->logger()->error($e->getMessage());
    }
  }

  public function generateTemplates(array $options = ['reset' => FALSE]) {
    try {
      if ($options['reset']) {
        $storage = $this->entityTypeManager->getStorage('requirement_type_template');
        $ids = $storage->getQuery()
          ->accessCheck(FALSE)
          ->execute();
        if (!empty($ids)) {
          $storage->delete($storage->loadMultiple($ids));
          $this->output()->writeln('Deleted existing templates.');
        }
      }

      $templates = [
        'core_course' => [
          'label' => 'Core Course Requirement',
          'type' => 'course',
          'weight' => 0,
          'config' => [
            'credits' => 3,
            'course_type' => 'core',
            'required' => TRUE,
            'minimum_grade' => 'B',
          ],
        ],
        'elective_course' => [
          'label' => 'Elective Course Requirement',
          'type' => 'course',
          'weight' => 10,
          'config' => [
            'credits' => 3,
            'course_type' => 'elective',
            'required' => FALSE,
            'minimum_grade' => 'C',
          ],
        ],
        'tool_proficiency' => [
          'label' => 'Tool Proficiency Requirement',
          'type' => 'skill',
          'weight' => 20,
          'config' => [
            'tools' => [
              'git' => [
                'label' => 'Git Version Control',
                'levels' => ['basic', 'intermediate', 'advanced'],
              ],
              'python' => [
                'label' => 'Python Programming',
                'levels' => ['basic', 'intermediate', 'advanced'],
              ],
              'r' => [
                'label' => 'R Statistical Computing',
                'levels' => ['basic', 'intermediate', 'advanced'],
              ],
            ],
            'minimum_level' => 'intermediate',
            'required_tools' => 2,
          ],
        ],
        'capstone_project' => [
          'label' => 'Capstone Project Requirement',
          'type' => 'project',
          'weight' => 30,
          'config' => [
            'milestones' => [
              'proposal' => [
                'label' => 'Project Proposal',
                'deadline' => '+2 months',
                'required' => TRUE,
              ],
              'interim_report' => [
                'label' => 'Interim Progress Report',
                'deadline' => '+4 months',
                'required' => TRUE,
              ],
              'final_presentation' => [
                'label' => 'Final Presentation',
                'deadline' => '+6 months',
                'required' => TRUE,
              ],
              'paper' => [
                'label' => 'Final Paper',
                'deadline' => '+6 months',
                'required' => TRUE,
                'minimum_length' => 5000,
              ],
            ],
            'advisor_approval_required' => TRUE,
          ],
        ],
      ];

      $storage = $this->entityTypeManager->getStorage('requirement_type_template');
      foreach ($templates as $id => $data) {
        try {
          $template = $storage->create([
            'id' => $id,
            'label' => $data['label'],
            'type' => $data['type'],
            'weight' => $data['weight'] ?? 0,
            'config' => $data['config'],
            'status' => TRUE,
          ]);
          $template->save();
          
          $this->output()->writeln(sprintf(
            'Created template: %s [%s]',
            $data['label'],
            $id
          ));
        }
        catch (\Exception $e) {
          $this->logger()->error(sprintf(
            'Failed to create template %s: %s',
            $id,
            $e->getMessage()
          ));
        }
      }

      $this->output()->writeln("\nUse 'drush dh-certificate:list-templates' to see all templates.");
    }
    catch (\Exception $e) {
      $this->logger()->error($e->getMessage());
      throw $e;
    }
  }

  /**
   * Command methods - Requirements
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
   * Generate requirement sets.
   *
   * @command dh-certificate:generate-requirement-sets
   * @aliases dhc-gen-req,dhc:gen-req
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

  /**
   * Generate standard requirements.
   *
   * @command dh-certificate:generate-standard-requirements 
   * @aliases dhc-gen-std-req,dhc:gen-std
   */
  public function generateStandardRequirements(array $options = ['reset' => FALSE]) {
    if ($options['reset']) {
      $this->output()->writeln('Removing existing requirements...');
      // Clear existing requirements
      $storage = $this->entityTypeManager->getStorage('requirement_set');
      $ids = $storage->getQuery()->accessCheck(FALSE)->execute();
      if (!empty($ids)) {
        $storage->delete($storage->loadMultiple($ids));
      }
    }

    $requirements = [
      'core_requirements' => [
        'label' => 'Core DH Requirements',
        'requirements' => [
          'methods_course' => [
            'type' => 'course',
            'label' => 'DH Methods Course',
            'required' => TRUE,
            'config' => [
              'course_type' => 'methods',
              'credits' => 3
            ]
          ],
          'core_courses' => [
            'type' => 'course',
            'label' => 'Core DH Courses',
            'required' => TRUE,
            'config' => [
              'minimum_credits' => 9,
              'course_type' => 'core'
            ]
          ]
        ]
      ],
      'technical_requirements' => [
        'label' => 'Technical Requirements',
        'requirements' => [
          'programming' => [
            'type' => 'tool',
            'label' => 'Programming Skills',
            'required' => TRUE,
            'config' => [
              'skills' => [
                'python' => 'Python Programming',
                'r' => 'R Statistical Computing',
                'javascript' => 'JavaScript Basics'
              ],
              'minimum_proficiency' => 2
            ]
          ],
          'version_control' => [
            'type' => 'tool',
            'label' => 'Version Control',
            'required' => TRUE,
            'config' => [
              'tools' => ['git'],
              'minimum_proficiency' => 1
            ]
          ]
        ]
      ],
      'project_requirements' => [
        'label' => 'Project Requirements',
        'requirements' => [
          'capstone' => [
            'type' => 'project',
            'label' => 'Capstone Project',
            'required' => TRUE,
            'config' => [
              'milestones' => [
                'proposal' => [
                  'label' => 'Project Proposal',
                  'deadline' => '+2 months'
                ],
                'progress' => [
                  'label' => 'Progress Report',
                  'deadline' => '+4 months'
                ],
                'final' => [
                  'label' => 'Final Presentation',
                  'deadline' => '+6 months'
                ]
              ]
            ]
          ]
        ]
      ]
    ];

    foreach ($requirements as $id => $data) {
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

  /**
   * Run complete setup with example data.
   *
   * @command dh-certificate:setup-all
   * @aliases dhc-setup,dhc-setup-all,dh:setup
   * @option reset Delete existing data before setup
   * @option uid User ID to generate enrollments for (defaults to 1)
   * @usage dh-certificate:setup-all --reset --uid=2
   *   Run complete setup with fresh data for user 2
   */
  public function setupAll(array $options = ['reset' => FALSE, 'uid' => 1]) {
    try {
      $this->output()->writeln('Starting complete DH Certificate setup...');

      // 1. Clean existing data if reset flag is set
      if ($options['reset']) {
        $this->output()->writeln('Cleaning existing data...');
        $this->cleanupProgress();
        $this->cleanupEnrollments();
        $this->deleteExistingTestData();
      }

      // 2. Generate requirement templates
      $this->output()->writeln("\nGenerating requirement templates...");
      $this->generateTemplates(['reset' => $options['reset']]);

      // 3. Generate requirement sets
      $this->output()->writeln("\nGenerating requirement sets...");
      $this->generateRequirementSets(['reset' => $options['reset']]);

      // 4. Generate standard requirements
      $this->output()->writeln("\nGenerating standard requirements...");
      $this->generateStandardRequirements(['reset' => $options['reset']]);

      // 5. Generate test courses and enrollments
      $this->output()->writeln("\nGenerating test courses and enrollments...");
      $user = $this->entityTypeManager->getStorage('user')->load($options['uid']);
      if (!$user) {
        throw new \Exception(sprintf('User %d not found', $options['uid']));
      }
      $this->generateTestData(['reset' => $options['reset'], 'uid' => $options['uid']]);

      // 6. Verify setup
      $this->output()->writeln("\nVerifying setup...");
      $status = $this->checkCertificateSetup();
      if ($status !== TRUE) {
        throw new \Exception("Setup verification failed: $status");
      }

      $this->output()->writeln("\n✅ Setup complete! Use these commands to explore:");
      // 7. Show debug info instead of progress check for now
      $this->output()->writeln("\nShowing debug information...");
      $this->debugEnrollments();

      $this->output()->writeln("\n✅ Setup complete! Use these commands to explore:");
      $this->output()->writeln("  drush dhc-progress {$options['uid']}     # View progress");
      $this->output()->writeln("  drush dhc-list-enroll        # List enrollments");
      $this->output()->writeln("  drush dhc-lt                 # List templates");
      $this->output()->writeln("  drush dhc-debug              # Debug info");

      return 0;
    }
    catch (\Exception $e) {
      $this->logger()->error($e->getMessage());
      $this->output()->writeln("<error>Setup failed: " . $e->getMessage() . "</error>");
      return 1;
    }
  }

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

}
