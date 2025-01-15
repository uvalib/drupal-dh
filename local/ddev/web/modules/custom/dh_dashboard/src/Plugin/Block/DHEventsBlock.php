<?php

namespace Drupal\dh_dashboard\Plugin\Block;

/**
 * Provides an events block for the Digital Humanities Dashboard.
 *
 * @Block(
 *   id = "dh_dashboard_events",
 *   admin_label = @Translation("DH Events"),
 *   category = @Translation("DH Dashboard"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user", required = FALSE)
 *   }
 * )
 */
class DHEventsBlock extends DHDashboardBlockBase {

  protected function getThemeHook(): string {
    return 'dh_dashboard_events';
  }

  protected function getBlockClass(): string {
    return 'block-dh-dashboard-events';
  }

  protected function getItemsPerPageConfigKey(): string {
    return 'events_items_per_page';
  }

  protected function getDisplayModeConfigKey(): string {
    return 'events_display_mode';
  }

  protected function getItems(): array {
    return [
      'items' => [
        [
          'title' => 'Digital Humanities Conference',
          'date' => '2024-03-15',
          'summary' => 'Annual conference featuring latest DH research',
          'type' => 'conference',
          'priority' => 'high',
          'icon' => 'users-class',
        ],
        // Add more events as needed
      ],
      'attributes' => [
        'class' => ['dh-events-block', 'events-grid', 'block-spacing'],
      ],
    ];
  }
}
