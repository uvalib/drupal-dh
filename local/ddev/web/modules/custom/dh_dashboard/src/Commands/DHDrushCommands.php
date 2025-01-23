<?php

namespace Drupal\dh_dashboard\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @deprecated Use \Drupal\dh_certificate\Commands\DHCertificateCommands instead.
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
   * @deprecated Use dh:check-dashboard instead.
   * @usage dh-dashboard:check-progress 1
   *   Check dashboard progress for user 1.
   */
  public function checkProgress($uid) {
    $this->output()->writeln('This command is deprecated. Please use dh:check-dashboard instead.');
    // Forward to new command
    return \Drupal::service('dh_certificate.commands')->checkDashboardProgress($uid);
  }

  /**
   * Reset dashboard data.
   *
   * @command dh-dashboard:reset
   * @aliases dh-reset
   * @deprecated Use dh:reset-all instead.
   * @usage dh-dashboard:reset
   *   Reset all dashboard data.
   */
  public function reset() {
    $this->output()->writeln('This command is deprecated. Please use dh:reset-all instead.');
    // Forward to new command
    return \Drupal::service('dh_certificate.commands')->resetAll();
  }

}
