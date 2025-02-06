<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_certificate\StructureMonitor\CourseStructureMonitor;
use Drupal\Core\Datetime\DateFormatterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for course structure monitoring.
 */
class CourseMonitorController extends ControllerBase {

  /**
   * The course structure monitor.
   *
   * @var \Drupal\dh_certificate\StructureMonitor\CourseStructureMonitor
   */
  protected $courseMonitor;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a CourseMonitorController object.
   *
   * @param \Drupal\dh_certificate\StructureMonitor\CourseStructureMonitor $course_monitor
   *   The course structure monitor.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   */
  public function __construct(
    CourseStructureMonitor $course_monitor,
    DateFormatterInterface $date_formatter
  ) {
    $this->courseMonitor = $course_monitor;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.course_structure_monitor'),
      $container->get('date.formatter')
    );
  }

  /**
   * Displays the course structure monitor page.
   */
  public function overview() {
    // Record current structure if not yet recorded
    if (!$this->courseMonitor->hasState()) {
      $this->courseMonitor->updateState();
      $this->messenger()->addStatus($this->t('Initial course structure recorded.'));
    }

    $changes = $this->courseMonitor->getChanges();
    $last_checked = $this->state->get('dh_certificate.course_structure_updated', 0);

    // Add monitor display
    return [
      '#theme' => 'dh_certificate_monitor_detail',
      '#monitor_id' => 'course',
      '#changes' => $changes,
      '#last_checked' => $last_checked,
      '#attached' => [
        'library' => ['dh_certificate/structure-monitor'],
      ],
    ];
  }

  /**
   * Records the current course structure.
   */
  public function recordStructure() {
    try {
      $this->courseMonitor->updateState();
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
  protected function getStructureSummary() {
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

}
