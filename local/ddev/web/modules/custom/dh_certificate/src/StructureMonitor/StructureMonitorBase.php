<?php

namespace Drupal\dh_certificate\StructureMonitor;

use Drupal\Core\State\StateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Base class for structure monitors.
 */
abstract class StructureMonitorBase implements StructureMonitorInterface {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Cached changes.
   *
   * @var array|null
   */
  protected $changes;

  /**
   * The state key for storing monitor data.
   *
   * @var string
   */
  protected $stateKey;

  /**
   * Constructs a new StructureMonitorBase.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   */
  public function __construct(StateInterface $state, LoggerChannelFactoryInterface $logger_factory) {
    $this->state = $state;
    $this->loggerFactory = $logger_factory;
    $this->stateKey = 'dh_certificate.monitor.' . $this->getMonitorId();
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    $this->state->delete($this->stateKey);
    $this->changes = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function updateState() {
    $current_state = $this->getCurrentState();
    $this->state->set($this->stateKey, $current_state);
    $this->changes = NULL;
  }

  /**
   * Get the monitor's unique identifier.
   *
   * @return string
   *   The monitor ID.
   */
  abstract protected function getMonitorId();

  /**
   * Get the current state of the monitored structure.
   *
   * @return array
   *   Array representing current state.
   */
  abstract protected function getCurrentState();

  /**
   * Get the logger channel.
   *
   * @return \Psr\Log\LoggerInterface
   *   The logger channel.
   */
  protected function getLogger() {
    return $this->loggerFactory->get('dh_certificate');
  }
}
