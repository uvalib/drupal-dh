<?php

namespace Drupal\dh_certificate\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\dh_certificate\Entity\RequirementInterface;

/**
 * Storage handler for requirement entities.
 */
class RequirementStorage extends SqlContentEntityStorage {

  /**
   * Gets requirements by type.
   *
   * @param string $type
   *   The requirement type.
   *
   * @return \Drupal\dh_certificate\Entity\RequirementInterface[]
   *   Array of matching requirements.
   */
  public function getByType($type) {
    return $this->loadByProperties(['requirement_type' => $type]);
  }

  /**
   * Gets requirements by set.
   *
   * @param string $set_id
   *   The requirement set ID.
   *
   * @return \Drupal\dh_certificate\Entity\RequirementInterface[]
   *   Array of requirements in the set.
   */
  public function getBySet($set_id) {
    return $this->loadByProperties(['requirement_set' => $set_id]);
  }

  /**
   * Gets required requirements.
   *
   * @return \Drupal\dh_certificate\Entity\RequirementInterface[]
   *   Array of required requirements.
   */
  public function getRequired() {
    return $this->loadByProperties(['required' => TRUE]);
  }

  /**
   * Gets requirements by validation status.
   *
   * @param bool $validated
   *   Whether to get validated or unvalidated requirements.
   *
   * @return \Drupal\dh_certificate\Entity\RequirementInterface[]
   *   Array of matching requirements.
   */
  public function getByValidationStatus($validated = TRUE) {
    $query = $this->getQuery();
    $query->condition('validation_criteria', '', $validated ? 'IS NOT NULL' : 'IS NULL');
    $ids = $query->execute();
    return $this->loadMultiple($ids);
  }
}
