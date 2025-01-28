<?php

namespace Drupal\dh_certificate\RequirementType;

interface RequirementTypeInterface {
  public function getId();
  public function getLabel();
  public function getWorkflowStates();
  public function validateProgress(array $progress_data);
  public function isComplete(array $progress_data);
}
