
<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for requirement type template entities.
 */
interface RequirementTypeTemplateInterface extends ConfigEntityInterface {

  /**
   * Gets the requirement type.
   *
   * @return string
   *   The requirement type.
   */
  public function getType();

  /**
   * Sets the requirement type.
   *
   * @param string $type
   *   The requirement type.
   *
   * @return $this
   */
  public function setType($type);

  /**
   * Gets the requirement configuration.
   *
   * @return array
   *   The requirement configuration.
   */
  public function getConfig();

  /**
   * Sets the requirement configuration.
   *
   * @param array $config
   *   The requirement configuration.
   *
   * @return $this
   */
  public function setConfig(array $config);

  /**
   * Gets the weight.
   *
   * @return int
   *   The weight.
   */
  public function getWeight();

  /**
   * Sets the weight.
   *
   * @param int $weight
   *   The weight.
   *
   * @return $this
   */
  public function setWeight($weight);

}