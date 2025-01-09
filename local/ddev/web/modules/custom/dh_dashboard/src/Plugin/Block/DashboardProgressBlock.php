<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Dashboard Progress' block.
 *
 * @Block(
 *   id = "dh_dashboard_progress",
 *   admin_label = @Translation("DH Dashboard Progress"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DashboardProgressBlock extends BlockBase {

  public function build() {
    return [
      '#theme' => 'dh_dashboard_progress',
      '#progress' => [
        'completed' => 2,
        'total' => 5,
        'courses' => [
          ['name' => 'Course 1', 'status' => 'completed'],
          ['name' => 'Course 2', 'status' => 'in-progress'],
        ],
      ],
    ];
  }

}
