<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @ContentEntityType(
 *   id = "certificate_progress",
 *   label = @Translation("Certificate Progress"),
 *   base_table = "certificate_progress",
 *   admin_permission = "administer dh certificate",
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "storage_schema" = "Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema",
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "uid"
 *   }
 * )
 */
class CertificateProgress extends ContentEntityBase {

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

    $fields['completed_courses'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Completed Courses'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'node')
      ->setSetting('handler_settings', ['target_bundles' => ['course' => 'course']]);

    $fields['last_updated'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Last Updated'))
      ->setRequired(TRUE)
      ->setDefaultValue(0);

    return $fields;
  }
}
