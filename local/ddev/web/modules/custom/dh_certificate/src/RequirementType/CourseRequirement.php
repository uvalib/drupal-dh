<?php

namespace Drupal\dh_certificate\RequirementType;

/**
 * @RequirementType(
 *   id = "course",
 *   label = @Translation("Course Requirement")
 * )
 */
class CourseRequirement implements RequirementTypeInterface {
  public function getWorkflowStates() {
    return [
      'not_started' => 'Not Started',
      'enrolled' => 'Enrolled',
      'in_progress' => 'In Progress',
      'completed' => 'Completed',
      'verified' => 'Verified by Advisor',
    ];
  }
  // ...existing code...
}
