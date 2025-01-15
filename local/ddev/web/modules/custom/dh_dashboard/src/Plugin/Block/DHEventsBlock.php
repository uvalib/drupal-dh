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
          'location' => 'Main Campus Conference Center',
          'type' => 'conference',
          'priority' => 'high',
          'status' => 'upcoming',
          'icon' => 'users',
          'url' => '/events/dh-conference-2024',
          'registration_status' => 'open',
          'category_class' => 'event-category--conference',
          'priority_class' => 'priority-indicator--high',
        ],
        [
          'title' => 'Digital Methods Workshop Series',
          'date' => '2024-03-20',
          'location' => 'Digital Lab Room 101',
          'type' => 'workshop',
          'priority' => 'medium',
          'status' => 'upcoming',
          'icon' => 'laptop-code',
          'url' => '/events/methods-workshop',
          'registration_status' => 'open',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
        ],
        [
          'title' => 'DH Project Showcase',
          'date' => '2024-04-05',
          'location' => 'University Library',
          'type' => 'exhibition',
          'priority' => 'high',
          'status' => 'upcoming',
          'icon' => 'project-diagram',
          'url' => '/events/project-showcase',
          'registration_status' => 'open',
          'category_class' => 'event-category--exhibition',
          'priority_class' => 'priority-indicator--high',
        ],
      ],
      'attributes' => [
        'class' => ['dh-events-block', 'events-grid', 'block-spacing'],
      ],
    ];
  }
}
