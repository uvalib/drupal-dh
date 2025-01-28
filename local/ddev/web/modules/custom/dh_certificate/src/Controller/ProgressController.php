<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for certificate progress management.
 */
class ProgressController extends ControllerBase {

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
      '#progress_data' => [],
    ];

    // Get progress data from service
    $progress_service = \Drupal::service('dh_certificate.progress');
    $build['#progress_data'] = $progress_service->getAllProgress();

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
    $database = \Drupal::database();
    
    // Improve query to ensure we get all fields and proper joins
    $query = $database->select('course_enrollment', 'ce');
    $query->leftJoin('node_field_data', 'n', 'ce.course_id = n.nid');
    $query->leftJoin('node__field_credits', 'fc', 'n.nid = fc.entity_id');
    $query->leftJoin('node__field_course_code', 'fcc', 'n.nid = fcc.entity_id');
    
    $query->fields('ce', ['id', 'status', 'completed_date'])
          ->fields('n', ['title', 'nid'])
          ->fields('fc', ['field_credits_value'])
          ->fields('fcc', ['field_course_code_value'])
          ->condition('ce.uid', $uid)
          ->orderBy('n.title');
    
    // Add debug logging
    \Drupal::logger('dh_certificate')->debug('User progress query: @query', [
      '@query' => $query->__toString()
    ]);
    
    $results = $query->execute()->fetchAll();
    \Drupal::logger('dh_certificate')->debug('Found @count enrollments', [
      '@count' => count($results)
    ]);

    // Calculate progress metrics
    $total_courses = count($results);
    $completed_courses = 0;
    $total_credits = 0;
    $completed_credits = 0;
    $rows = [];
    $courses = [];

    foreach ($results as $row) {
      $completed = $row->completed_date ? date('Y-m-d', $row->completed_date) : '—';
      $credits = (int)$row->field_credits_value ?? 0;
      $mnemonic = $row->field_course_code_value ? "({$row->field_course_code_value})" : '';
      
      $rows[] = [
        $row->title . ' ' . $mnemonic,
        ucfirst($row->status),
        $completed,
      ];

      $courses[] = [
        'title' => $row->title,
        'mnemonic' => $row->field_course_code_value,
        'status' => $row->status,
        'credits' => $credits,
        'completed_date' => $completed,
      ];
      
      $total_credits += $credits;
      if ($row->status === 'completed') {
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
    $database = \Drupal::database();
    
    // Query to get all enrollments with user and course data
    $query = $database->select('course_enrollment', 'ce');
    $query->join('users_field_data', 'u', 'ce.uid = u.uid');
    $query->join('node_field_data', 'n', 'ce.course_id = n.nid');
    $query->fields('ce', ['uid', 'status', 'completed_date'])
          ->fields('u', ['name'])
          ->fields('n', ['title', 'nid'])
          ->orderBy('u.name')
          ->orderBy('n.title');
    
    $results = $query->execute()->fetchAll();
    
    // Group enrollments by user
    $enrollments = [];
    foreach ($results as $record) {
      $enrollments[] = [
        'user' => $record->name,
        'course' => $record->title,
        'status' => ucfirst($record->status),
        'completed_date' => $record->completed_date ? date('Y-m-d', $record->completed_date) : '—',
      ];
    }

    // Build render array
    $build = [
      '#theme' => 'admin_enrollments',
      '#enrollments' => $enrollments,
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
