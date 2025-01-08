<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a DH News Block.
 *
 * @Block(
 *   id = "dh_dashboard_news",
 *   admin_label = @Translation("DH News"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DHNewsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'dh_dashboard_news',
      '#news_items' => $this->getNewsItems(),
    ];
  }

  protected function getNewsItems() {
    // Mock data - replace with actual news query
    return [
      ['title' => 'New DH Course Available', 'date' => '2024-01-15'],
      ['title' => 'Digital Humanities Symposium', 'date' => '2024-02-01'],
    ];
  }
}