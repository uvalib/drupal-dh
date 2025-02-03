<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_certificate\ProgressManagerInterface;
use Drupal\dh_certificate\CourseStructureMonitor;
use Drupal\Core\Datetime\DateFormatterInterface;

/**
 * Controller for certificate administration pages.
 */
class AdminController extends ControllerBase {

  /**
   * The progress manager.
   *
   * @var \Drupal\dh_certificate\ProgressManagerInterface
   */
  protected $progressManager;

  /**
   * The course structure monitor.
   *
   * @var \Drupal\dh_certificate\CourseStructureMonitor
   */
  protected $courseMonitor;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs an AdminController object.
   *
   * @param \Drupal\dh_certificate\ProgressManagerInterface $progress_manager
   *   The progress manager.
   * @param \Drupal\dh_certificate\CourseStructureMonitor $course_monitor
   *   The course structure monitor.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   */
  public function __construct(
    ProgressManagerInterface $progress_manager,
    CourseStructureMonitor $course_monitor,
    DateFormatterInterface $date_formatter
  ) {
    $this->progressManager = $progress_manager;
    $this->courseMonitor = $course_monitor;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.progress_manager'),
      $container->get('dh_certificate.course_structure_monitor'),
      $container->get('date.formatter')
    );
  }

  /**
   * Displays the course structure monitor page.
   */
  public function courseStructureMonitor() {
    // Record current structure if not yet recorded
    if (!$this->courseMonitor->hasRecordedStructure()) {
      $this->courseMonitor->recordCurrentStructure();
      $this->messenger()->addStatus($this->t('Initial course structure recorded.'));
    }

    $changes = $this->courseMonitor->getStructureChanges();
    $critical = $this->courseMonitor->checkCriticalChanges();

    // Add action buttons
    $build['actions'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['course-monitor-actions']],
      'record' => [
        '#type' => 'link',
        '#title' => $this->t('Record Current Structure'),
        '#url' => \Drupal\Core\Url::fromRoute('dh_certificate.course_monitor_record'),
        '#attributes' => ['class' => ['button', 'button--primary']],
      ],
    ];

    // Add monitor display
    $build['monitor'] = [
      '#theme' => 'dh_certificate_course_monitor',
      '#changes' => $changes,
      '#critical' => $critical,
      '#last_checked' => $changes['last_checked'],
      '#attached' => [
        'library' => ['dh_certificate/course-monitor'],
      ],
    ];

    // Add summary section
    $build['summary'] = [
      '#type' => 'details',
      '#title' => $this->t('Course Structure Summary'),
      '#open' => TRUE,
      'content' => [
        '#theme' => 'item_list',
        '#items' => $this->getCourseStructureSummary(),
      ],
    ];

    return $build;
  }

  /**
   * Records the current course structure and redirects back to monitor.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response object.
   */
  public function recordStructure() {
    try {
      $this->courseMonitor->recordCurrentStructure();
      $this->messenger()->addStatus($this->t('Course structure has been recorded.'));
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Error recording course structure: @error', [
        '@error' => $e->getMessage(),
      ]));
      \Drupal::logger('dh_certificate')->error('Error recording course structure: @error', [
        '@error' => $e->getMessage(),
      ]);
    }

    return $this->redirect('dh_certificate.course_monitor');
  }

  /**
   * Gets a summary of the course structure.
   */
  protected function getCourseStructureSummary() {
    $structure = $this->courseMonitor->getCurrentStructure();
    $summary = [];

    if (!empty($structure['fields'])) {
      $field_types = [];
      foreach ($structure['fields'] as $field) {
        $field_types[$field['type']] = ($field_types[$field['type']] ?? 0) + 1;
      }

      $summary[] = $this->t('Total fields: @count', [
        '@count' => count($structure['fields']),
      ]);

      foreach ($field_types as $type => $count) {
        $summary[] = $this->t('@type fields: @count', [
          '@type' => $type,
          '@count' => $count,
        ]);
      }
    }

    return $summary;
  }

  /**
   * Displays an overview of certificate administration.
   */
  public function overview() {
    $stats = $this->getOverviewStats();
    $course_changes = $this->courseMonitor->getStructureChanges();
    
    // Check for critical changes
    $critical_changes = $this->courseMonitor->checkCriticalChanges();
    if (!empty($critical_changes)) {
      $this->messenger()->addWarning($this->t('Critical course structure changes detected. <a href="@url">View details</a>', [
        '@url' => \Drupal\Core\Url::fromRoute('dh_certificate.course_monitor')->toString(),
      ]));
    }

    return [
      '#theme' => 'dh_certificate_admin_overview',
      '#stats' => $stats,
      '#course_changes' => !empty($course_changes['added']) || 
                          !empty($course_changes['removed']) || 
                          !empty($course_changes['modified']),
      '#critical_changes' => !empty($critical_changes),
      '#attached' => [
        'library' => ['dh_certificate/certificate-admin'],
      ],
    ];
  }

  /**
   * Gets overview statistics.
   *
   * @return array
   *   Array of overview statistics.
   */
  protected function getOverviewStats() {
    return [
      'total_students' => $this->getTotalStudents(),
      'active_courses' => $this->getActiveCourses(),
      'progress_summary' => $this->progressManager->getReports()['summary'],
    ];
  }

  /**
   * Gets the total number of enrolled students.
   */
  protected function getTotalStudents() {
    return $this->entityTypeManager()
      ->getStorage('user')
      ->getQuery()
      ->condition('status', 1)
      ->condition('roles', 'student')
      ->accessCheck(TRUE)
      ->count()
      ->execute();
  }

  /**
   * Gets the number of active courses.
   */
  protected function getActiveCourses() {
    return $this->entityTypeManager()
      ->getStorage('node')
      ->getQuery()
      ->condition('type', 'course')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->count()
      ->execute();
  }

}
