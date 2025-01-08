<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a DH Certificate Progress Block.
 *
 * @Block(
 *   id = "dh_dashboard_progress",
 *   admin_label = @Translation("Certificate Progress"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DHProgressBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $progress = $this->getProgress();
    
    return [
      '#theme' => 'dh_dashboard_progress',
      '#progress' => $progress,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  protected function getProgress() {
    // Mock data - replace with actual progress calculation
    return [
      'completed' => 20,
      'total' => 62,
      'courses' => [
        ['name' => 'DH 101', 'status' => 'completed'],
        ['name' => 'DH 201', 'status' => 'in_progress'],
      ],
    ];
  }
}