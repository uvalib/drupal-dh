<?php

namespace Drupal\dh_certificate\RequirementType;

/**
 * Interface for the RequirementTypeManager.
 */
interface RequirementTypeManagerInterface {

  /**
   * Gets the requirement type.
   *
   * @param string $type
   *   The requirement type.
   *
   * @return mixed
   *   The requirement type instance.
   */
  public function getRequirementType($type);

  /**
   * Validates the progress data for a requirement type.
   *
   * @param array $data
   *   The progress data.
   *
   * @return bool
   *   TRUE if the data is valid, FALSE otherwise.
   */
  public function validateProgress(array $data);

}