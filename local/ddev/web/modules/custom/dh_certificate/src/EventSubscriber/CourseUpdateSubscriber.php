<?php

namespace Drupal\dh_certificate\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Entity\EntityEvents;
use Drupal\Core\Entity\EntityInterface;

class CourseUpdateSubscriber implements EventSubscriberInterface {

  protected $entityTypeManager;
  protected $logger;

  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger_factory->get('dh_certificate');
  }

  public static function getSubscribedEvents() {
    return [
      EntityEvents::UPDATE => 'onCourseUpdate',
      EntityEvents::DELETE => 'onCourseDelete',
    ];
  }

  public function onCourseUpdate(EntityInterface $entity) {
    if ($entity->getEntityTypeId() !== 'node' || $entity->bundle() !== 'course') {
      return;
    }

    // Find and update certificate course mappings
    $storage = $this->entityTypeManager->getStorage('certificate_course');
    $mappings = $storage->loadByProperties(['course_id' => $entity->id()]);

    foreach ($mappings as $mapping) {
      if (!$mapping->verifyReference()) {
        $this->logger->warning('Invalid course reference found in certificate mapping: @id', ['@id' => $mapping->id()]);
        // Optionally disable or delete invalid mappings
      }
    }
  }

  public function onCourseDelete(EntityInterface $entity) {
    if ($entity->getEntityTypeId() !== 'node' || $entity->bundle() !== 'course') {
      return;
    }

    // Remove certificate course mappings for deleted courses
    $storage = $this->entityTypeManager->getStorage('certificate_course');
    $mappings = $storage->loadByProperties(['course_id' => $entity->id()]);
    
    if ($mappings) {
      $storage->delete($mappings);
      $this->logger->notice('Removed @count certificate mappings for deleted course @id', [
        '@count' => count($mappings),
        '@id' => $entity->id()
      ]);
    }
  }
}
