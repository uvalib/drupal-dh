<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @ContentEntityType(
 *   id = "student_progress",
 *   label = @Translation("Student Progress"),
 *   base_table = "student_progress",
 *   admin_permission = "administer dh certificate",
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "uid"
 *   }
 * )
 */
class StudentProgress extends ContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setReadOnly(TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setSetting('target_type', 'user')
      ->setRequired(TRUE);

    $fields['progress_data'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Progress Data'))
      ->setDescription(t('Serialized progress data for the student.'));

    $fields['last_activity'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Last Activity'))
      ->setRequired(TRUE)
      ->setDefaultValue(0);

    return $fields;
  }
}
