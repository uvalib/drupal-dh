<?php

namespace Drupal\dh_dashboard\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\profile\Entity\Profile;

/**
 * Defines an interface for DH Requirement plugins.
 */
interface DHRequirementInterface extends PluginInspectionInterface {

  /**
   * Returns the type of the requirement.
   *
   * @return string
   *   The requirement type (course or general).
   */
  public function getType();

  /**
   * Returns the label of the requirement.
   *
   * @return string
   *   The requirement label.
   */
  public function getLabel();

  /**
   * Returns the ID of the requirement.
   *
   * @return string
   *   The requirement ID.
   */
  public function getId();

  /**
   * Checks if the requirement is completed.
   *
   * @param \Drupal\profile\Entity\Profile $profile
   *   The user's certificate profile.
   *
   * @return bool
   *   TRUE if completed, FALSE otherwise.
   */
  public function isCompleted(Profile $profile);

  /**
   * Gets additional metadata for the requirement.
   *
   * @param \Drupal\profile\Entity\Profile $profile
   *   The user's certificate profile.
   *
   * @return array
   *   An array of metadata.
   */
  public function getMetadata(Profile $profile);
}
