<?php

namespace Drupal\dh_dashboard\Plugin\Block;

/**
 * Provides an information block for the Digital Humanities Dashboard.
 *
 * @Block(
 *   id = "dh_dashboard_info",
 *   admin_label = @Translation("DH Info"),
 *   category = @Translation("DH Dashboard"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user", required = FALSE)
 *   }
 * )
 */
class DHInfoBlock extends DHDashboardBlockBase {

  protected function getThemeHook(): string {
    return 'dh_dashboard_info';
  }

  protected function getBlockClass(): string {
    return 'block-dh-dashboard-info';
  }

  protected function getItemsPerPageConfigKey(): string {
    return 'info_items_per_page';
  }

  protected function getDisplayModeConfigKey(): string {
    return 'info_display_mode';
  }

  protected function getItems(): array {
    return [
      'items' => [
        [
          'title' => 'Program Requirements',
          'type' => 'requirements',
          'summary' => 'Complete guide to certificate requirements and policies',
          'priority' => 'high',
          'category' => 'academic',
          'icon' => 'graduation-cap',
          'url' => '/info/requirements',
          'category_class' => 'info-category--academic',
          'priority_class' => 'priority-indicator--high',
        ],
        [
          'title' => 'Research Resources',
          'type' => 'resources',
          'summary' => 'Digital tools and databases for research',
          'priority' => 'medium',
          'category' => 'research',
          'icon' => 'database',
          'url' => '/info/resources',
          'category_class' => 'info-category--research',
          'priority_class' => 'priority-indicator--medium',
        ],
        [
          'title' => 'Faculty Directory',
          'type' => 'directory',
          'summary' => 'Directory of DH faculty and research interests',
          'priority' => 'medium',
          'category' => 'contact',
          'icon' => 'users',
          'url' => '/info/faculty',
          'category_class' => 'info-category--contact',
          'priority_class' => 'priority-indicator--medium',
        ],
      ],
      'attributes' => [
        'class' => ['dh-info-block', 'info-grid', 'block-spacing'],
      ],
    ];
  }
}
