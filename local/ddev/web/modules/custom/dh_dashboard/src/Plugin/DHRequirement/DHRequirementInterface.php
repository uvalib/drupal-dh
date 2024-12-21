<?php

namespace Drupal\dh_dashboard\Plugin\DHRequirement;

interface DHRequirementInterface {
  public function getId();
  public function getLabel();
  public function getType();
  public function getWeight();
  public function isCompleted($profile);
  public function getMetadata($profile);
}
