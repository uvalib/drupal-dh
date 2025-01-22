<?php

namespace Drupal\dh_certificate\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\UserInterface;

class DHCertificateProgressManager {
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public function getUserProgress(UserInterface $user) {
    try {
      if (!$user->hasField('dh_certificate_progress')) {
        throw new \Exception('Certificate progress field not found on user');
      }
      $progress = $user->get('dh_certificate_progress')->entity;
      if (!$progress) {
        $progress = $this->createProgressEntity($user);
      }
      return $progress;
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error($e->getMessage());
      return NULL;
    }
  }

  protected function createProgressEntity(UserInterface $user) {
    $progress = $this->entityTypeManager->getStorage('dh_certificate_progress')->create([
      'uid' => $user->id(),
    ]);
    $progress->save();
    $user->set('dh_certificate_progress', $progress);
    $user->save();
    return $progress;
  }

  public function addCompletedCourse(UserInterface $user, $course_id) {
    $progress = $this->getUserProgress($user);
    $completed_courses = $progress->get('completed_courses')->getValue();
    $completed_courses[] = ['target_id' => $course_id];
    $progress->set('completed_courses', $completed_courses);
    $progress->save();
  }
}
