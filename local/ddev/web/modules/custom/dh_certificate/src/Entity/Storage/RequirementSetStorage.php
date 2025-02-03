<?php

namespace Drupal\dh_certificate\Entity\Storage;

use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\dh_certificate\Entity\RequirementSetInterface;

/**
 * Storage handler for requirement sets.
 */
class RequirementSetStorage extends ConfigEntityStorage {

  /**
   * Gets active requirement sets.
   *
   * @return \Drupal\dh_certificate\Entity\RequirementSetInterface[]
   *   Array of active requirement sets.
   */
  public function getActiveSets() {
    return $this->loadByProperties(['status' => TRUE]);
  }

  /**
   * Gets requirement sets for an academic year.
   *
   * @param string $year
   *   The academic year.
   *
   * @return \Drupal\dh_certificate\Entity\RequirementSetInterface[]
   *   Array of requirement sets for the academic year.
   */
  public function getSetsByAcademicYear($year) {
    return $this->loadByProperties(['academic_year' => $year]);
  }

  /**
   * Gets sets containing a specific requirement.
   *
   * @param string $requirement_id
   *   The requirement ID.
   *
   * @return \Drupal\dh_certificate\Entity\RequirementSetInterface[]
   *   Array of requirement sets containing the requirement.
   */
  public function getSetsByRequirement($requirement_id) {
    $sets = $this->loadMultiple();
    return array_filter($sets, function (RequirementSetInterface $set) use ($requirement_id) {
      $requirements = $set->getRequirements();
      foreach ($requirements as $requirement) {
        if ($requirement['id'] === $requirement_id) {
          return TRUE;
        }
      }
      return FALSE;
    });
  }

}
