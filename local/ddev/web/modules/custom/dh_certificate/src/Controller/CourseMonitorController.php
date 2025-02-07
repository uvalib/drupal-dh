<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_certificate\StructureMonitor\CourseStructureMonitor;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\State\StateInterface;
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
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a CourseMonitorController object.
   *
   * @param \Drupal\dh_certificate\StructureMonitor\CourseStructureMonitor $course_monitor
   *   The course structure monitor.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(
    CourseStructureMonitor $course_monitor,
    DateFormatterInterface $date_formatter,
    StateInterface $state
  ) {
    $this->courseMonitor = $course_monitor;
    $this->dateFormatter = $date_formatter;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.course_structure_monitor'),
      $container->get('date.formatter'),
      $container->get('state')
    );
  }

  /**
   * Displays the course structure monitor page.
   */
  public function overview() {
    $build = [];

    if (!$this->courseMonitor->hasState()) {
      $this->courseMonitor->updateState();
      $this->messenger()->addStatus($this->t('Initial course structure recorded.'));
    }

    $changes = $this->courseMonitor->getChanges();
    $last_checked = $this->state->get('dh_certificate.course_structure_updated', 0);
    $structure_data = $this->courseMonitor->getCurrentStructure();

    return [
      '#theme' => 'dh_certificate_monitor_detail',
      '#monitor_id' => 'course',
      '#changes' => $changes,
      '#last_checked' => $last_checked,
      '#structure_data' => [
        '#theme' => 'dh_certificate_structure_data',
        '#entity_type' => 'course',
        '#structure_data' => $structure_data,
        '#last_updated' => $last_checked,
      ],
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

  /**
   * Displays structure data for a given type.
   *
   * @param string $type
   *   The type of structure to display (course, taxonomy, etc).
   *
   * @return array
   *   Render array for the structure data.
   */
  public function structureData($type) {
    $structure_data = [];
    $last_updated = 0;

    switch ($type) {
      case 'course':
        $structure_data = $this->courseMonitor->getCurrentStructure();
        $last_updated = $this->state->get('dh_certificate.course_structure_updated', 0);
        break;

      // Add other structure types here as needed
    }

    return [
      '#theme' => 'dh_certificate_structure_data',
      '#entity_type' => $type,
      '#structure_data' => $structure_data,
      '#last_updated' => $last_updated,
      '#attached' => [
        'library' => ['dh_certificate/structure-monitor'],
      ],
    ];
  }

}
