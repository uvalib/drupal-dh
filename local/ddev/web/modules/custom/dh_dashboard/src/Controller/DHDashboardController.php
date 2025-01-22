<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for DH Dashboard functionality.
 */
class DHDashboardController extends ControllerBase {

  public function blockUsage() {
    $blocks = [
      'dh_dashboard_news' => 'DH News Block',
      'dh_dashboard_events' => 'DH Events Block',
      'dh_dashboard_info' => 'DH Info Block',
      'dh_dashboard_program_info' => 'Program Info Block',
      'dh_dashboard_certificate_info' => 'Certificate Info Block',
    ];

    $usage = [];
    
    foreach ($blocks as $id => $name) {
      $usage[$id] = [
        'name' => $name,
        'layout_builder' => $this->getLayoutBuilderPlacements($id),
      ];
    }

    return [
      '#theme' => 'dh_dashboard_block_usage',
      '#usage' => $usage,
      '#cache' => ['max-age' => 0],
    ];
  }

  protected function getLayoutBuilderPlacements($plugin_id) {
    $database = \Drupal::database();
    $query = $database->select('config', 'c')
      ->fields('c', ['name', 'data'])
      ->condition('name', '%layout_builder%', 'LIKE');
    
    $results = $query->execute();
    $placements = [];
    
    foreach ($results as $record) {
      if ($data = unserialize($record->data)) {
        if (is_string($data) && strpos($data, $plugin_id) !== FALSE) {
          $placements[] = [
            'config' => $record->name,
            'section' => $this->getLayoutSection($data),
          ];
        }
      }
    }
    
    return $placements;
  }

  protected function getLayoutSection($data) {
    // Extract section information from layout builder data
    // This is a simplified version - you might want to add more detail
    return [
      'type' => 'layout_builder',
      'data' => substr($data, 0, 100) . '...',
    ];
  }
}
