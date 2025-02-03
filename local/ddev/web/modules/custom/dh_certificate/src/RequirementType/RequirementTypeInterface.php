<?php

namespace Drupal\dh_certificate\RequirementType;

/**
 * Interface for requirement types.
 */
interface RequirementTypeInterface {

  /**
   * Gets the requirement type ID.
   *
   * @return string
   *   The requirement type ID.
   */
  public function getId();

  /**
   * Gets the requirement type label.
   *
   * @return string
   *   The requirement type label.
   */
  public function getLabel();

  /**
   * Gets the workflow states for this requirement type.
   *
   * @return array
   *   Array of workflow states with machine names as keys and labels as values.
   */
  public function getWorkflowStates();

  /**
   * Validates requirement data.
   *
   * @param array $data
   *   The requirement data to validate.
   *
   * @return bool
   *   TRUE if valid, FALSE otherwise.
   */
  public function validateData(array $data);
}
