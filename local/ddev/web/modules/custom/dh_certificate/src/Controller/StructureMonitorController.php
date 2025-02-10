<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\dh_certificate\StructureMonitor\CourseStructureMonitor;
use Drupal\dh_certificate\StructureMonitor\ProfileStructureMonitor;
use Drupal\dh_certificate\StructureMonitor\TaxonomyStructureMonitor;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Controller for structure monitoring.
 */
class StructureMonitorController extends ControllerBase {

  /**
   * The course structure monitor.
   *
   * @var \Drupal\dh_certificate\StructureMonitor\CourseStructureMonitor
   */
  protected $courseMonitor;

  /**
   * The profile structure monitor.
   *
   * @var \Drupal\dh_certificate\StructureMonitor\ProfileStructureMonitor
   */
  protected $profileMonitor;

  /**
   * The taxonomy structure monitor.
   *
   * @var \Drupal\dh_certificate\StructureMonitor\TaxonomyStructureMonitor
   */
  protected $taxonomyMonitor;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a new StructureMonitorController.
   *
   * @param \Drupal\dh_certificate\StructureMonitor\CourseStructureMonitor $course_monitor
   *   The course structure monitor.
   * @param \Drupal\dh_certificate\StructureMonitor\ProfileStructureMonitor $profile_monitor
   *   The profile structure monitor.
   * @param \Drupal\dh_certificate\StructureMonitor\TaxonomyStructureMonitor $taxonomy_monitor
   *   The taxonomy structure monitor.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(
    CourseStructureMonitor $course_monitor,
    ProfileStructureMonitor $profile_monitor,
    TaxonomyStructureMonitor $taxonomy_monitor,
    StateInterface $state
  ) {
    $this->courseMonitor = $course_monitor;
    $this->profileMonitor = $profile_monitor;
    $this->taxonomyMonitor = $taxonomy_monitor;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.course_structure_monitor'),
      $container->get('dh_certificate.profile_structure_monitor'),
      $container->get('dh_certificate.taxonomy_structure_monitor'),
      $container->get('state')
    );
  }

  /**
   * Displays an overview of all structure monitors.
   */
  public function overview() {
    return [
      '#theme' => 'dh_certificate_monitor_overview',
      '#monitors' => [
        'course' => [
          'title' => $this->t('Course Structure'),
          'description' => $this->t('Monitor changes to course content type structure, fields, and displays.'),
          'url' => Url::fromRoute('dh_certificate.course_monitor'),
          'last_check' => $this->state->get('dh_certificate.course_monitor.last_check', 0),
          'changes' => $this->getChangeCount('course'),
        ],
        'profile' => [
          'title' => $this->t('Profile Structure'),
          'description' => $this->t('Monitor changes to user profile fields and settings.'),
          'url' => Url::fromRoute('dh_certificate.profile_monitor'),
          'last_check' => $this->state->get('dh_certificate.profile_monitor.last_check', 0),
          'changes' => $this->getChangeCount('profile'),
        ],
        'taxonomy' => [
          'title' => $this->t('Taxonomy Structure'),
          'description' => $this->t('Monitor changes to certificate-related taxonomies.'),
          'url' => Url::fromRoute('dh_certificate.taxonomy_monitor'),
          'last_check' => $this->state->get('dh_certificate.taxonomy_monitor.last_check', 0),
          'changes' => $this->getChangeCount('taxonomy'),
        ],
      ],
      '#attached' => [
        'library' => ['dh_certificate/structure-monitor'],
      ],
    ];
  }

  private function getChangeCount($type) {
    // TODO: Implement actual change detection
    return rand(0, 5); // Temporary for testing
  }

  /**
   * Resets all structure monitors.
   *
   * @param string|null $monitor_id
   *   Optional monitor ID to reset specific monitor.
   */
  public function reset($monitor_id = NULL) {
    if ($monitor_id) {
      switch ($monitor_id) {
        case 'course':
          $this->courseMonitor->reset();
          $message = $this->t('Course structure monitor has been reset.');
          break;
        case 'profile':
          $this->profileMonitor->reset();
          $message = $this->t('Profile structure monitor has been reset.');
          break;
        case 'taxonomy':
          $this->taxonomyMonitor->reset();
          $message = $this->t('Taxonomy structure monitor has been reset.');
          break;
        default:
          return $this->redirect('dh_certificate.structure_monitor');
      }
      $this->messenger()->addStatus($message);
    }
    else {
      // Reset all monitors
      $this->courseMonitor->reset();
      $this->profileMonitor->reset();
      $this->taxonomyMonitor->reset();
      $this->messenger()->addStatus($this->t('All structure monitors have been reset.'));
    }
    
    return new RedirectResponse(Url::fromRoute('dh_certificate.structure_monitor')->toString());
  }

  /**
   * Refreshes the course structure monitor.
   */
  public function refreshCourse() {
    $this->courseMonitor->refresh();
    $changes = $this->courseMonitor->getChanges();
    
    if (!empty($changes)) {
      $this->messenger()->addStatus($this->t('Course structure monitor refreshed. Found @count changes.', [
        '@count' => count($changes),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('Course structure monitor refreshed. No changes found.'));
    }
    
    return new RedirectResponse(Url::fromRoute('dh_certificate.course_monitor')->toString());
  }

  /**
   * Refreshes the profile structure monitor.
   */
  public function refreshProfile() {
    $this->profileMonitor->refresh();
    $changes = $this->profileMonitor->getChanges();
    
    if (!empty($changes)) {
      $this->messenger()->addStatus($this->t('Profile structure monitor refreshed. Found @count changes.', [
        '@count' => count($changes),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('Profile structure monitor refreshed. No changes found.'));
    }
    
    return new RedirectResponse(Url::fromRoute('dh_certificate.profile_monitor')->toString());
  }

  /**
   * Refreshes the taxonomy structure monitor.
   */
  public function refreshTaxonomy() {
    $this->taxonomyMonitor->refresh();
    $changes = $this->taxonomyMonitor->getChanges();
    
    if (!empty($changes)) {
      $this->messenger()->addStatus($this->t('Taxonomy structure monitor refreshed. Found @count changes.', [
        '@count' => count($changes),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('Taxonomy structure monitor refreshed. No changes found.'));
    }
    
    return new RedirectResponse(Url::fromRoute('dh_certificate.taxonomy_monitor')->toString());
  }

  /**
   * Returns the detailed view for a specific monitor.
   *
   * @param string $monitor_id
   *   The monitor ID ('course', 'profile', or 'taxonomy').
   * @param string|null $record_id
   *   Optional record ID for viewing specific record.
   */
  public function detail($monitor_id = NULL, $record_id = NULL) {
    $monitor = NULL;
    switch ($monitor_id) {
      case 'course':
        $monitor = $this->courseMonitor;
        break;
      case 'profile':
        $monitor = $this->profileMonitor;
        break;
      case 'taxonomy':
        $monitor = $this->taxonomyMonitor;
        break;
    }

    if (!$monitor) {
      return $this->redirect('dh_certificate.structure_monitor');
    }

    $build = [
      '#theme' => 'dh_certificate_monitor_detail',
      '#monitor' => [
        'id' => $monitor_id,
        'title' => $this->getMonitorTitle($monitor_id),
        'last_check' => $monitor->getLastCheck(),
        'changes' => $monitor->getChanges(),
        'data' => $monitor->getData(),
        'view_type' => $record_id ? 'record' : 'list',
      ],
    ];

    if ($record_id) {
      $build['#monitor']['record_id'] = $record_id;
    }

    return $build;
  }

  /**
   * Helper function to get monitor title.
   */
  protected function getMonitorTitle($monitor_id) {
    switch ($monitor_id) {
      case 'course':
        return $this->t('Course Structure Monitor');
      case 'profile':
        return $this->t('Profile Structure Monitor');
      case 'taxonomy':
        return $this->t('Taxonomy Structure Monitor');
      default:
        return $this->t('Structure Monitor');
    }
  }

  /**
   * Generates record link URL.
   *
   * @param string $monitor_id
   *   The monitor ID.
   * @param string $timestamp
   *   The record timestamp to use as record_id.
   *
   * @return \Drupal\Core\Url
   *   The URL object.
   */
  protected function getRecordUrl($monitor_id, $timestamp) {
    return Url::fromRoute("dh_certificate.{$monitor_id}_monitor_record", [
      'record_id' => $timestamp,
    ]);
  }

  /**
   * Build table of changes.
   */
  protected function buildChangesTable($changes, $monitor_id, $timestamp = NULL) {
    $rows = [];
    foreach ($changes as $change) {
      $row = [
        'change' => [
          'data' => [
            '#markup' => $change,
          ],
        ],
      ];
      
      if ($timestamp) {
        $row['operations'] = [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'view' => [
                'title' => $this->t('View'),
                'url' => $this->getRecordUrl($monitor_id, $timestamp),
              ],
            ],
          ],
        ];
      }
      
      $rows[] = $row;
    }

    // ...existing code...
  }

}
