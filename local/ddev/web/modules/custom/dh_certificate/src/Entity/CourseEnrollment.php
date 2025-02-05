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
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "access" = "Drupal\dh_certificate\CourseEnrollmentAccessControlHandler",
 *     "list_builder" = "Drupal\dh_certificate\CourseEnrollmentListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\dh_certificate\Form\CourseEnrollmentForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     }
 *   },
 *   base_table = "course_enrollment",
 *   translatable = FALSE,
 *   admin_permission = "administer dh certificate",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "uid",
 *     "status" = "status"
 *   },
 *   links = {
 *     "canonical" = "/certificate/enrollment/{course_enrollment}",
 *     "edit-form" = "/certificate/enrollment/{course_enrollment}/edit",
 *     "delete-form" = "/certificate/enrollment/{course_enrollment}/delete"
 *   }
 * )
 */
class CourseEnrollment extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The enrollment ID.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The enrollment UUID.'))
      ->setReadOnly(TRUE);

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

    $fields['enrolled_date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Enrolled Date'))
      ->setDescription(t('The date the user enrolled.'))
      ->setDefaultValue(0)
      ->setRequired(TRUE);

    return $fields;
  }
}
