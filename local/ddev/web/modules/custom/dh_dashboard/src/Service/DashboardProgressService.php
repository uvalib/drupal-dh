<?php

namespace Drupal\dh_dashboard\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Service for handling dashboard progress data.
 */
class DashboardProgressService {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new DashboardProgressService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    AccountProxyInterface $current_user
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * Gets the progress data for a user.
   *
   * @return array
   *   The progress data.
   */
  public function getUserProgress() {
    return [
      'total' => 0,
      'completed' => 0,
      'in_progress' => 0,
      'not_started' => 0,
      'registered' => 0,
      'completed_percentage' => 0,
      'in_progress_percentage' => 0,
      'not_started_percentage' => 0,
      'registered_percentage' => 0,
      'courses' => [],
    ];
  }

}
