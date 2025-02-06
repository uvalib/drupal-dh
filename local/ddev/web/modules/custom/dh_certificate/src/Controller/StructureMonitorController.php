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
    $monitors = [
      'course' => [
        'id' => 'course',
        'title' => $this->t('Course Structure Monitor'),
        'status' => $this->t('Active'),
        'last_checked' => time(),
        'changes' => $this->getChangeCount('course'),
      ],
      'taxonomy' => [
        'id' => 'taxonomy',
        'title' => $this->t('Taxonomy Monitor'),
        'status' => $this->t('Active'),
        'last_checked' => time(),
        'changes' => $this->getChangeCount('taxonomy'),
      ],
      'profile' => [
        'id' => 'profile',
        'title' => $this->t('Profile Monitor'),
        'status' => $this->t('Active'),
        'last_checked' => time(),
        'changes' => $this->getChangeCount('profile'),
      ],
    ];

    return [
      '#theme' => 'dh_certificate_monitor_overview',
      '#monitors' => $monitors,
      '#attached' => [
        'library' => ['dh_certificate/structure-monitor'],
      ],
    ];
  }

  private function getChangeCount($type) {
    // TODO: Implement actual change detection
    return rand(0, 5); // Temporary for testing
  }

  public function detail($monitor_id) {
    $data = $this->getMonitorData($monitor_id);
    
    return [
      '#theme' => 'dh_certificate_monitor_detail',
      '#monitor_id' => $monitor_id,
      '#changes' => $data,
      '#last_checked' => time(),
    ];
  }

  private function getMonitorData($type) {
    switch ($type) {
      case 'course':
        return $this->getCourseStructure();
      case 'taxonomy':
        return $this->getTaxonomyStructure();
      case 'profile':
        return $this->getProfileStructure();
      default:
        return [];
    }
  }

  private function getCourseStructure() {
    // TODO: Implement actual course structure retrieval
    return ['Course structure data will go here'];
  }

  private function getTaxonomyStructure() {
    // TODO: Implement actual taxonomy structure retrieval
    return ['Taxonomy structure data will go here'];
  }

  private function getProfileStructure() {
    // TODO: Implement actual profile structure retrieval
    return ['Profile structure data will go here'];
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
