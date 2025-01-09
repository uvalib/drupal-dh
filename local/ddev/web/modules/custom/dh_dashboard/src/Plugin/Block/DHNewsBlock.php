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
    $news = $this->getNews();
    
    return [
      '#theme' => 'dh_dashboard_news',
      '#news' => $news,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  protected function getNews() {
    return [
      'items' => [
        [
          'title' => 'New Digital Archives Course Available',
          'date' => '2024-02-15',
          'summary' => 'Explore digital preservation techniques in our new course DH 305: Digital Archives and Preservation.',
          'category' => 'courses',
          'priority' => 'high',
        ],
        [
          'title' => 'Summer Digital Humanities Workshop Series',
          'date' => '2024-02-10',
          'summary' => 'Register now for our intensive summer workshops covering text analysis, data visualization, and digital mapping.',
          'category' => 'events',
          'priority' => 'medium',
        ],
        [
          'title' => 'Certificate Requirements Updated',
          'date' => '2024-02-01',
          'summary' => 'New elective options added for the 2024-25 academic year. Check your progress dashboard for details.',
          'category' => 'program',
          'priority' => 'high',
        ],
        [
          'title' => 'Student Project Showcase',
          'date' => '2024-01-28',
          'summary' => 'View outstanding digital humanities projects from this semester\'s graduating cohort.',
          'category' => 'events',
          'priority' => 'medium',
        ],
      ],
    ];
  }
}