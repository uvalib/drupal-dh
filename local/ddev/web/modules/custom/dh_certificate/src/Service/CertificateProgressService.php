<?php

namespace Drupal\dh_certificate\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;

class CertificateProgressService {
  protected $entityTypeManager;
  protected $currentUser;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  public function calculateCompletion($uid) {
    // Query for all available courses
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->condition('status', 1);
    $course_ids = $query->execute();
    
    $courses = $this->entityTypeManager->getStorage('node')->loadMultiple($course_ids);
    
    $progress = [];
    foreach ($courses as $course) {
      $progress[$course->id()] = [
        'title' => $course->label(),
        'completed' => FALSE,
        'progress' => 0,
      ];
    }
    
    return $progress;
  }
}
