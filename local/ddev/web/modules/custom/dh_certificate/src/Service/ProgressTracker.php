<?php

namespace Drupal\dh_certificate\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;

class ProgressTracker {

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public function updateProgress($uid) {
    $storage = $this->entityTypeManager->getStorage('certificate_progress');
    $query = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('uid', $uid)
      ->execute();
    $progress = empty($query) ? 
      $storage->create(['uid' => $uid]) : 
      $storage->load(reset($query));

    // Get completed courses
    $completed_courses = $this->entityTypeManager
      ->getStorage('course_enrollment')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('uid', $uid)
      ->condition('status', 'completed')
      ->execute();

    $course_ids = array_map(function ($enrollment) {
      return $enrollment->get('course_id')->target_id;
    }, $completed_courses);

    $progress->set('completed_courses', $course_ids);
    $progress->set('last_updated', \Drupal::time()->getRequestTime());
    return $progress->save();
  }

  public function getStudentProgress($uid) {
    return $this->entityTypeManager
      ->getStorage('student_progress')
      ->loadByProperties(['uid' => $uid]);
  }
}
