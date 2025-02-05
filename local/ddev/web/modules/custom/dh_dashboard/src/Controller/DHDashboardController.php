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
    $config_factory = \Drupal::configFactory();
    $placements = [];
    
    // Get all config that might contain layout builder settings
    $config_names = $config_factory->listAll();
    foreach ($config_names as $name) {
      if (strpos($name, 'layout_builder') !== FALSE) {
        $config = $config_factory->get($name);
        $data = $config->getRawData();
        
        if (is_array($data) && $this->configContainsPlugin($data, $plugin_id)) {
          $placements[] = [
            'config' => $name,
            'section' => $this->getLayoutSection($data),
          ];
        }
      }
    }
    
    return $placements;
  }

  protected function configContainsPlugin(array $data, $plugin_id) {
    $json = json_encode($data);
    return strpos($json, $plugin_id) !== FALSE;
  }

  protected function getLayoutSection($data) {
    // Extract section information from layout builder data
    // This is a simplified version - you might want to add more detail
    return [
      'type' => 'layout_builder',
      'data' => substr(json_encode($data), 0, 100) . '...',
    ];
  }
}
