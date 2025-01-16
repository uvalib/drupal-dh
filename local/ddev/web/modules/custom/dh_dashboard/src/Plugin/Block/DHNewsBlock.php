<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;

/**
 * Provides a news block for the Digital Humanities Dashboard.
 *
 * @Block(
 *   id = "dh_dashboard_news",
 *   admin_label = @Translation("DH News"),
 *   category = @Translation("DH Dashboard"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user", required = FALSE)
 *   }
 * )
 */
class DHNewsBlock extends DHDashboardBlockBase {

  protected function getThemeHook(): string {
    return 'dh_dashboard_news';
  }

  protected function getBlockClass(): string {
    return 'dh-news-block';
  }

  protected function getItemsPerPageConfigKey(): string {
    return 'news_items_per_page';
  }

  protected function getDisplayModeConfigKey(): string {
    return 'news_display_mode';
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['category_filter'] = [
      '#type' => 'select',
      '#title' => $this->t('Category filter'),
      '#default_value' => $config['category_filter'],
      '#options' => [
        'all' => $this->t('All categories'),
        'program' => $this->t('Program'),
        'events' => $this->t('Events'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['category_filter'] = $form_state->getValue('category_filter');
  }

  protected function getItems(): array {
    $items = [
      'items' => $this->getMockNewsItems(),
      'attributes' => new Attribute([
        'class' => ['dh-news-block', 'news-grid', 'block-spacing']
      ]),
    ];

    return $items;
  }

  protected function getMockNewsItems(): array {
    $news_items = [];
    
    $mock_data = [
      [
        'title' => 'New Digital Humanities Research Center Opening',
        'category' => 'announcement',
        'priority' => 'high',
        'icon' => 'building',
        'days_ago' => 0,
      ],
      [
        'title' => 'Latest Computational Analysis Methods Published',
        'category' => 'publication',
        'priority' => 'medium',
        'icon' => 'book',
        'days_ago' => 1,
      ],
      [
        'title' => 'Student Research Showcase',
        'category' => 'event',
        'priority' => 'high',
        'icon' => 'users',
        'days_ago' => 2,
      ],
      [
        'title' => 'Digital Archives Workshop Series',
        'category' => 'workshop',
        'priority' => 'medium',
        'icon' => 'archive',
        'days_ago' => 3,
      ],
      [
        'title' => 'Grant Opportunity: Digital Preservation',
        'category' => 'grant',
        'priority' => 'high',
        'icon' => 'dollar-sign',
        'days_ago' => 4,
      ],
      [
        'title' => 'Virtual Reality in Humanities Research',
        'category' => 'research',
        'priority' => 'medium',
        'icon' => 'vr-cardboard',
        'days_ago' => 5,
      ],
      [
        'title' => 'Text Mining Workshop Registration Open',
        'category' => 'workshop',
        'priority' => 'medium',
        'icon' => 'file-alt',
        'days_ago' => 6,
      ],
      [
        'title' => 'Data Visualization Competition Results',
        'category' => 'announcement',
        'priority' => 'high',
        'icon' => 'chart-bar',
        'days_ago' => 7,
      ],
      [
        'title' => 'Machine Learning for Historical Analysis',
        'category' => 'research',
        'priority' => 'high',
        'icon' => 'robot',
        'days_ago' => 8,
      ],
    ];

    foreach ($mock_data as $item) {
      $news_items[] = [
        'title' => $item['title'],
        'date' => date('Y-m-d', strtotime("-{$item['days_ago']} days")),
        'description' => "This is a detailed news item about " . ucfirst($item['category']) . 
                        " in Digital Humanities. This showcases our ongoing work and developments.",
        'category' => $item['category'],
        'priority' => $item['priority'],
        'category_class' => 'news-category--' . $item['category'],
        'priority_class' => 'priority-indicator--' . $item['priority'],
        'icon' => $item['icon'],
        'url' => "/news/" . strtolower(str_replace([' ', ':'], ['-', ''], $item['title'])),
      ];
    }

    return $news_items;
  }

  /**
   * Gets formatted user data for display.
   */
  protected function getUserData($account) {
    // ... existing getUserData implementation ...
  }

  public function build() {
    $build = parent::build();
    
    // Override the items key to match the template's expected 'news' variable
    $build['#news'] = $build['#items'];
    unset($build['#items']);

    // Only display debug info if explicitly enabled in both config and build
    $config = $this->configFactory->get('dh_dashboard.settings');
    $build['#show_debug'] = !empty($build['#show_debug']) && $config->get('show_debug');

    return $build;
  }
}