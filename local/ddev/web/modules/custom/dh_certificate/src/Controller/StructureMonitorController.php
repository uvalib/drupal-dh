<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\dh_certificate\StructureMonitor\CourseStructureMonitor;
use Drupal\dh_certificate\StructureMonitor\ProfileStructureMonitor;
use Drupal\dh_certificate\StructureMonitor\TaxonomyStructureMonitor;

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
      '#theme' => 'dh_certificate_structure_monitor',
      '#content' => [
        'status' => $this->courseMonitor->hasChanged(),
        'changes' => $this->courseMonitor->getChanges(),
        'monitors' => [
          'course' => [
            'status' => $this->courseMonitor->hasChanged(),
            'changes' => $this->courseMonitor->getChanges(),
          ],
          'profile' => [
            'status' => $this->profileMonitor->hasChanged(),
            'changes' => $this->profileMonitor->getChanges(),
          ],
          'taxonomy' => [
            'status' => $this->taxonomyMonitor->hasChanged(),
            'changes' => $this->taxonomyMonitor->getChanges(),
          ],
        ],
      ],
    ];
  }

  /**
   * Displays details for a specific monitor.
   */
  public function detail($monitor_id) {
    if (!isset($this->monitors[$monitor_id])) {
      return $this->redirect('dh_certificate.monitor_overview');
    }

    $monitor = $this->monitors[$monitor_id];
    $changes = $monitor->getChanges();

    return [
      '#theme' => 'dh_certificate_monitor_detail',
      '#monitor_id' => $monitor_id,
      '#changes' => $changes,
      '#last_checked' => $this->state()->get('dh_certificate.monitor.' . $monitor_id . '.last_check', 0),
      '#attached' => [
        'library' => ['dh_certificate/structure-monitor'],
      ],
    ];
  }

  /**
   * Resets a monitor's state.
   */
  public function reset($monitor_id) {
    if (isset($this->monitors[$monitor_id])) {
      $this->monitors[$monitor_id]->reset();
      $this->messenger()->addStatus($this->t('Monitor state has been reset.'));
    }
    return $this->redirect('dh_certificate.monitor_overview');
  }

}
