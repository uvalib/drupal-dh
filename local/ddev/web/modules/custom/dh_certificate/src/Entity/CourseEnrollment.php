<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Course Enrollment entity.
 *
 * @ContentEntityType(
 *   id = "course_enrollment",
 *   label = @Translation("Course Enrollment"),
 *   base_table = "course_enrollment",
 *   admin_permission = "administer dh certificate",
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "storage_schema" = "Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema",
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uid" = "uid",
 *     "status" = "status"
 *   },
 *   links = {
 *     "delete-form" = "/admin/dh_certificate/enrollment/{course_enrollment}/delete",
 *     "collection" = "/admin/dh_certificate/enrollments"
 *   }
 * )
 */
class CourseEnrollment extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setDescription(t('The user ID of the enrolled student.'))
      ->setSetting('target_type', 'user')
      ->setRequired(TRUE);

    $fields['course_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Course'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'node')
      ->setSetting('handler_settings', ['target_bundles' => ['course' => 'course']]);

    $fields['status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Status'))
      ->setRequired(TRUE)
      ->setDefaultValue('pending')
      ->setSettings([
        'max_length' => 32,
        'text_processing' => 0,
      ]);

    $fields['completed_date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Completion Date'))
      ->setRequired(FALSE);

    return $fields;
  }
}
