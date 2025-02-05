<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for Requirement Set configuration entities.
 */
interface RequirementSetInterface extends ConfigEntityInterface {

  /**
   * Gets the Requirement Set description.
   *
   * @return string
   *   The description text.
   */
  public function getDescription();

  /**
   * Sets the Requirement Set description.
   *
   * @param string $description
   *   The description text.
   *
   * @return $this
   */
  public function setDescription($description);

  /**
   * Gets the requirements for this set.
   *
   * @return array
   *   Array of requirement configurations.
   */
  public function getRequirements();

  /**
   * Sets the requirements for this set.
   *
   * @param array $requirements
   *   Array of requirement configurations.
   *
   * @return $this
   */
  public function setRequirements(array $requirements);

  /**
   * Gets the weight of this requirement set.
   *
   * @return int
   *   The weight value.
   */
  public function getWeight();

  /**
   * Sets the weight of this requirement set.
   *
   * @param int $weight
   *   The weight value.
   *
   * @return $this
   */
  public function setWeight($weight);

  /**
   * Gets whether this requirement set is enabled.
   *
   * @return bool
   *   TRUE if enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the enabled status of this requirement set.
   *
   * @param bool $status
   *   TRUE to enable, FALSE to disable.
   *
   * @return $this
   */
  public function setStatus($status);
}