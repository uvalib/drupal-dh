<?php

namespace Drupal\dh_certificate\StructureMonitor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Base class for entity structure monitors.
 */
abstract class EntityStructureMonitorBase extends StructureMonitorBase {
  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new EntityStructureMonitorBase.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory,
    StateInterface $state
  ) {
    parent::__construct($state, $logger_factory);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get the monitor's unique identifier.
   *
   * @return string
   *   The monitor ID.
   */
  abstract public function getMonitorId();

  /**
   * Calculate differences between previous and current states.
   *
   * @param array $previous
   *   Previous state data.
   * @param array $current
   *   Current state data.
   *
   * @return array
   *   Array of changes.
   */
  abstract protected function calculateChanges(array $previous, array $current);
}
