<?php

namespace Drupal\dh_certificate;

/**
 * Interface for structure monitors.
 */
interface StructureMonitorInterface {

  /**
   * Gets detected structural changes.
   *
   * @return array
   *   Array of changes, each containing:
   *   - type: The change type (ADDED, MODIFIED, REMOVED)
   *   - entity_type: The affected entity type
   *   - entity_id: The affected entity ID
   *   - description: Human readable change description
   *   - timestamp: Change detection timestamp
   */
  public function getChanges();

  /**
   * Records the current structure state.
   */
  public function recordCurrentStructure();

  /**
   * Resets the monitor state.
   */
  public function reset();

}
