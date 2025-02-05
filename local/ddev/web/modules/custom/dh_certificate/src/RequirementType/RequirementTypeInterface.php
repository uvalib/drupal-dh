<?php

namespace Drupal\dh_certificate\RequirementType;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Interface for requirement type plugins.
 */
interface RequirementTypeInterface extends PluginInspectionInterface {

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
  public function getLabel(): string;

  /**
   * Gets the requirement type description.
   *
   * @return string
   *   The requirement type description.
   */
  public function getDescription(): string;

  /**
   * Gets the requirement type weight.
   *
   * @return int
   *   The requirement type weight.
   */
  public function getWeight(): int;

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
