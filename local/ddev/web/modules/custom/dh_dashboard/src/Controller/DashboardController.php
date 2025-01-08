<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the DH Dashboard.
 */
class DashboardController extends ControllerBase {

  /**
   * Displays the dashboard content.
   *
   * @return array
   *   Render array for the dashboard.
   */
  public function content() {
    $node = $this->getDefaultDashboard();
    
    // Build the full node render array with layout builder enabled
    $build = $this->entityTypeManager()
      ->getViewBuilder('node')
      ->view($node, 'default');

    $build['#cache']['max-age'] = 0;
    
    return $build;
  }

  /**
   * Gets or creates the default dashboard node.
   *
   * @return \Drupal\node\NodeInterface
   *   The dashboard node.
   */
  protected function getDefaultDashboard() {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nodes = $storage->loadByProperties([
      'type' => 'student_dashboard',
      'title' => 'Default Dashboard',
    ]);
    
    if (!empty($nodes)) {
      return reset($nodes);
    }

    // Create default dashboard if it doesn't exist
    $node = $storage->create([
      'type' => 'student_dashboard',
      'title' => 'Default Dashboard',
      'status' => 1,
    ]);
    $node->save();
    
    return $node;
  }
}
