<?php

namespace Drupal\dh_certificate\Structure;

/**
 * Interface for structure monitoring services.
 */
interface StructureMonitorInterface {

  /**
   * Gets changes in the monitored structure.
   *
   * @return array
   *   Array of changes with type, entity info, and description.
   */
  public function getChanges();

  /**
   * Records the current structure state.
   */
  public function recordCurrentStructure();

  /**
   * Resets the structure monitoring.
   */
  public function reset();

}
