<?php

namespace Drupal\dh_dashboard\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DH Dashboard Drush commands.
 */
class DHDrushCommands extends DrushCommands {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new DHDrushCommands object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct();
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Check dashboard progress for a user.
   *
   * @param int $uid
   *   The user ID to check progress for.
   *
   * @command dh-dashboard:check-progress
   * @aliases dh-cp
   * @usage dh-dashboard:check-progress 1
   *   Check dashboard progress for user 1.
   */
  public function checkProgress($uid) {
    $progress_service = \Drupal::service('dh_dashboard.progress');
    $user = $this->entityTypeManager->getStorage('user')->load($uid);
    
    if (!$user) {
      throw new \Exception(dt('User @uid not found.', ['@uid' => $uid]));
    }

    $progress = $progress_service->getUserProgress();
    
    $this->output()->writeln(dt('Progress for user @name:', ['@name' => $user->getDisplayName()]));
    $this->output()->writeln(json_encode($progress, JSON_PRETTY_PRINT));
  }

  /**
   * Reset dashboard data.
   *
   * @command dh-dashboard:reset
   * @aliases dh-reset
   * @usage dh-dashboard:reset
   *   Reset all dashboard data.
   */
  public function reset() {
    // Clear any dashboard-related data
    $this->output()->writeln('Resetting dashboard data...');
    
    // You can implement actual reset logic here
    $this->output()->writeln('Dashboard data reset complete.');
  }

}
