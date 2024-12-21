<?php

namespace Drupal\dh_dashboard\Commands;

use Drush\Commands\DrushCommands;
use Drupal\dh_dashboard\Services\DashboardCleanupService;

/**
 * Dashboard module Drush commands.
 */
class DashboardCommands extends DrushCommands {

  protected $cleanupService;

  public function __construct(DashboardCleanupService $cleanup_service) {
    $this->cleanupService = $cleanup_service;
  }

  /**
   * Clean up all dashboard module entities and configurations.
   *
   * @command dh-dashboard:cleanup
   * @aliases dhd-cleanup
   * @usage drush dh-dashboard:cleanup
   */
  public function cleanup() {
    $stats = $this->cleanupService->cleanup();
    
    foreach ($stats['entities'] as $type => $count) {
      $this->logger()->success(dt('Cleaned up @count @type entities.', [
        '@count' => $count,
        '@type' => $type,
      ]));
    }
    
    $this->logger()->success(dt('Cleaned up @count configurations.', [
      '@count' => $stats['configs'],
    ]));
  }
}
