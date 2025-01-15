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
          'summary' => 'Essential information about program completion',
          'priority' => 'high',
          'icon' => 'graduation-cap',
        ],
        // Add more info items as needed
      ],
      'attributes' => [
        'class' => ['dh-info-block', 'info-grid', 'block-spacing'],
      ],
    ];
  }
}
