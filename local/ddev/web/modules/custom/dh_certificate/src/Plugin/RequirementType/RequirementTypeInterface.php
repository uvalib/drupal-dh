
<?php

namespace Drupal\dh_certificate\Plugin\RequirementType;

interface RequirementTypeInterface {
  public function calculateProgress($uid);
  public function getState($uid);
  public function validateCompletion($uid);
  public function getDueDate($uid);
}