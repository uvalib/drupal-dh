<?php

namespace Drupal\dh_certificate\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;

class EnrollmentManager {
  
  protected $entityTypeManager;
  protected $currentUser;

  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    AccountInterface $current_user
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  public function enrollUser($uid, $course_id) {
    $storage = $this->entityTypeManager->getStorage('course_enrollment');
    $enrollment = $storage->create([
      'uid' => $uid,
      'course_id' => $course_id,
      'status' => 'pending',
      'enrolled_date' => \Drupal::time()->getRequestTime(),
    ]);
    return $enrollment->save();
  }

  public function getUserEnrollments($uid) {
    return $this->entityTypeManager
      ->getStorage('course_enrollment')
      ->loadByProperties(['uid' => $uid]);
  }

  public function markCourseComplete($uid, $course_id) {
    $enrollments = $this->entityTypeManager
      ->getStorage('course_enrollment')
      ->loadByProperties([
        'uid' => $uid,
        'course_id' => $course_id,
      ]);
    
    if ($enrollment = reset($enrollments)) {
      $enrollment->set('status', 'completed');
      $enrollment->set('completed_date', \Drupal::time()->getRequestTime());
      return $enrollment->save();
    }
    return FALSE;
  }
}
