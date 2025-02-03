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
}
