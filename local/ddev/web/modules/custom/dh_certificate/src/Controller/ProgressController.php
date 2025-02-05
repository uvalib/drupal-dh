<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dh_certificate\Progress\ProgressManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for certificate progress management.
 */
class ProgressController extends ControllerBase {

  /**
   * The progress manager service.
   *
   * @var \Drupal\dh_certificate\Progress\ProgressManagerInterface
   */
  protected $progressManager;

  /**
   * Constructs a new ProgressController.
   *
   * @param \Drupal\dh_certificate\Progress\ProgressManagerInterface $progress_manager
   *   The progress manager.
   */
  public function __construct(ProgressManagerInterface $progress_manager) {
    $this->progressManager = $progress_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.progress')
    );
  }

  /**
   * Displays an overview of all certificate progress.
   *
   * @return array
   *   A render array representing the progress overview page.
   */
  public function adminOverview() {
    $build = [
      '#theme' => 'dh_certificate_progress_overview',
      '#title' => $this->t('Certificate Progress Overview'),
      '#progress_data' => $this->progressManager->getAllProgress(),
    ];

    return $build;
  }

  /**
   * Displays user progress towards certificate completion.
   *
   * @return array
   *   Render array for the progress page.
   */
  public function userProgress() {
    $uid = $this->currentUser()->id();
    $enrollment_storage = $this->entityTypeManager()->getStorage('course_enrollment');
    
    // Load enrollments using Entity API
    $query = $enrollment_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('uid', $uid)
      ->execute();
    
    $enrollments = $enrollment_storage->loadMultiple($query);
    
    // Calculate progress metrics
    $total_courses = count($enrollments);
    $completed_courses = 0;
    $total_credits = 0;
    $completed_credits = 0;
    $rows = [];
    $courses = [];

    foreach ($enrollments as $enrollment) {
      $course = $enrollment->get('course_id')->entity;
      if (!$course) {
        continue;
      }

      $completed = $enrollment->get('completed_date')->value 
        ? date('Y-m-d', $enrollment->get('completed_date')->value) 
        : '—';
      $credits = (int)$course->get('field_credits')->value ?? 0;
      $mnemonic = $course->get('field_course_code')->value ? 
        "({$course->get('field_course_code')->value})" : '';
      
      $rows[] = [
        $course->label() . ' ' . $mnemonic,
        ucfirst($enrollment->get('status')->value),
        $completed,
      ];

      $courses[] = [
        'title' => $course->label(),
        'mnemonic' => $course->get('field_course_code')->value,
        'status' => $enrollment->get('status')->value,
        'credits' => $credits,
        'completed_date' => $completed,
      ];
      
      $total_credits += $credits;
      if ($enrollment->get('status')->value === 'completed') {
        $completed_courses++;
        $completed_credits += $credits;
      }
    }

    $progress_data = [
      'total_courses' => $total_courses,
      'completed_courses' => $completed_courses,
      'total_credits' => $total_credits,
      'completed_credits' => $completed_credits,
      'percentage' => $total_courses > 0 ? round(($completed_courses / $total_courses) * 100) : 0,
      'courses' => $courses,
    ];

    $build = [
      '#type' => 'container',
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('My Certificate Progress'),
      ],
      'enrollments' => [
        '#type' => 'details',
        '#title' => $this->t('Current Enrollments'),
        '#open' => TRUE,
        'table' => [
          '#type' => 'table',
          '#header' => [
            $this->t('Course'),
            $this->t('Status'),
            $this->t('Completed Date'),
          ],
          '#rows' => $rows,
          '#empty' => $this->t('No course enrollments found.'),
        ],
      ],
      'debug' => [
        '#type' => 'details',
        '#title' => $this->t('Debug Information'),
        '#open' => FALSE,
        'data' => [
          '#type' => 'html_tag',
          '#tag' => 'pre',
          '#value' => json_encode($progress_data, JSON_PRETTY_PRINT),
        ],
      ],
      '#cache' => [
        'contexts' => ['user'],
      ],
    ];
    
    return $build;
  }

  /**
   * Displays a list of enrollments for each user.
   *
   * @return array
   *   Render array for the enrollments page.
   */
  public function adminEnrollments() {
    $enrollment_storage = $this->entityTypeManager()->getStorage('course_enrollment');
    
    // Get all enrollments using Entity API
    $query = $enrollment_storage->getQuery()
      ->accessCheck(FALSE)
      ->sort('course_id')
      ->execute();
    
    $enrollments = $enrollment_storage->loadMultiple($query);
    
    // Group enrollments by user
    $enrollment_data = [];
    foreach ($enrollments as $enrollment) {
      $user = $enrollment->get('uid')->entity;
      $course = $enrollment->get('course_id')->entity;
      
      if (!$user || !$course) {
        continue;
      }

      $enrollment_data[] = [
        'user' => $user->getDisplayName(),
        'course' => $course->label(),
        'status' => ucfirst($enrollment->get('status')->value),
        'completed_date' => $enrollment->get('completed_date')->value ? 
          date('Y-m-d', $enrollment->get('completed_date')->value) : '—',
      ];
    }

    // Build render array
    $build = [
      '#theme' => 'admin_enrollments',
      '#enrollments' => $enrollment_data,
      '#cache' => [
        'contexts' => ['user'],
      ],
    ];

    return $build;
  }

  /**
   * Displays the admin progress overview.
   *
   * @return array
   *   A render array representing the admin progress overview page.
   */
  public function adminProgress() {
    $build = [
      '#theme' => 'dh_certificate_admin_progress',
      '#title' => $this->t('Certificate Progress Overview'),
      '#attached' => [
        'library' => ['dh_certificate/certificate-admin'],
      ],
    ];

    // Load progress data
    $progress_data = $this->getProgressData();

    // Structure the data for the template
    $build['#progress'] = $progress_data;

    return $build;
  }

  /**
   * Gets progress data for the admin overview.
   *
   * @return array
   *   An array of progress data.
   */
  protected function getProgressData() {
    // Implement logic to fetch and structure progress data
    // This is a placeholder implementation
    return [
      'total_students' => 100,
      'completed_courses' => 200,
      'in_progress_courses' => 50,
      'pending_courses' => 30,
    ];
  }

}
