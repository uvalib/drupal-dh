<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a Dashboard News Feed block.
 *
 * @Block(
 *   id = "dh_dashboard_news",
 *   admin_label = @Translation("Dashboard News Feed"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DashboardNewsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'news')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0, 5)
      ->accessCheck(TRUE);
    
    $nids = $query->execute();
    $news_items = Node::loadMultiple($nids);
    
    $items = [];
    foreach ($news_items as $news) {
      $body = $news->hasField('body') && !$news->get('body')->isEmpty() 
        ? $news->get('body')->first()->getValue()['summary'] 
        : '';
        
      $items[] = [
        '#type' => 'markup',
        '#markup' => '<div class="news-item">'
          . '<h3>' . $news->getTitle() . '</h3>'
          . '<div class="news-date">' . date('M d, Y', $news->getCreatedTime()) . '</div>'
          . '<div class="news-summary">' . $body . '</div>'
          . '</div>',
      ];
    }

    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#cache' => [
        'max-age' => 3600,
        'tags' => ['node_list:news'],
      ],
    ];
  }
}
