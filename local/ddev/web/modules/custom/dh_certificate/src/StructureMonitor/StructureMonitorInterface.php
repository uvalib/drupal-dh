<?php

namespace Drupal\dh_certificate\StructureMonitor;

/**
 * Interface for structure monitoring services.
 */
interface StructureMonitorInterface {

  /**
   * Check if the monitored structure has changed.
   *
   * @return bool
   *   TRUE if changes detected, FALSE otherwise.
   */
  public function hasChanged();

  /**
   * Get list of changes since last check.
   *
   * @return array
   *   Array of changes with descriptions.
   */
  public function getChanges();

  /**
   * Update the stored state after changes are processed.
   *
   * @return void
   */
  public function updateState();

  /**
   * Reset the monitor state.
   *
   * @return void
   */
  public function reset();

  /**
   * Gets the timestamp of the last state update.
   *
   * @return int
   *   Unix timestamp of last check, 0 if never checked.
   */
  public function getLastCheck();

  /**
   * Gets the current stored state data.
   *
   * @return array
   *   The current state data.
   */
  public function getData();

  /**
   * Refreshes the monitor state and checks for changes.
   *
   * @return bool
   *   TRUE if changes were detected, FALSE otherwise.
   */
  public function refresh();
}
