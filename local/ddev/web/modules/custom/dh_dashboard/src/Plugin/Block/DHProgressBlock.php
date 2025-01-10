<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Template\Attribute;

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
      '#attributes' => new Attribute(['class' => ['dh-progress-block']]),
      '#label_display' => 'FALSE',
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  protected function getProgress() {
    return [
      'completed' => 20,
      'total' => 62,
      'percentage' => 32,
      'status_class' => 'progress-status--in-progress',
      'courses' => [
        [
          'name' => 'DH 101',
          'status' => 'completed',
          'status_class' => 'course-status--completed',
          'icon' => 'check-circle',
        ],
        [
          'name' => 'DH 201',
          'status' => 'in_progress',
          'status_class' => 'course-status--in-progress',
          'icon' => 'clock',
        ],
      ],
      'attributes' => [
        'class' => ['dh-progress-block', 'block-spacing'],
      ],
    ];
  }
}