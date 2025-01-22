<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @ContentEntityType(
 *   id = "dh_certificate_progress",
 *   label = @Translation("DH Certificate Progress"),
 *   base_table = "dh_certificate_progress",
 *   entity_keys = {
 *     "id" = "id",
 *     "uid" = "uid",
 *   },
 * )
 */
class DHCertificateProgress extends ContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'user');

    $fields['certificate_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Certificate'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'node')
      ->setSetting('handler_settings', ['target_bundles' => ['certificate' => 'certificate']]);
      
    $fields['progress_data'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Progress Data'))
      ->setDescription(t('Stores progress tracking data as serialized array'))
      ->setDefaultValue([]);

    $fields['completed_courses'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Completed Courses'))
      ->setCardinality(-1)
      ->setSetting('target_type', 'node');

    $fields['project_status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Project Status'))
      ->setDefaultValue(FALSE);

    $fields['due_date'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Due Date'))
      ->setDescription(t('When this requirement needs to be completed'))
      ->setSettings([
        'max_length' => 32,
        'text_processing' => 0,
      ]);

    $fields['due_date_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Due Date Type'))
      ->setDescription(t('Calendar or Academic term'))
      ->setSettings([
        'max_length' => 32,
        'text_processing' => 0,
      ]);

    return $fields;
  }
}
