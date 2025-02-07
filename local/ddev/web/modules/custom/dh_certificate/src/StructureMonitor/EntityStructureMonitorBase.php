<?php

namespace Drupal\dh_certificate\StructureMonitor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Base class for entity structure monitors.
 */
abstract class EntityStructureMonitorBase implements StructureMonitorInterface {
  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Array of detected changes.
   *
   * @var array
   */
  protected $changes = [];

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
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger_factory->get('dh_certificate');
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public function hasChanged() {
    $previous_state = $this->state->get($this->stateKey, []);
    $current_state = $this->getCurrentState();
    
    return $previous_state != $current_state;
  }

  /**
   * {@inheritdoc}
   */
  public function getChanges() {
    $current = $this->getCurrentState();
    $previous = $this->state->get('dh_certificate.' . $this->getMonitorId(), []);
    $this->changes = $this->calculateChanges($previous, $current);
    return $this->changes;
  }

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
