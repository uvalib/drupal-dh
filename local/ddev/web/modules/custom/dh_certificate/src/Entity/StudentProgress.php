<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @ContentEntityType(
 *   id = "student_progress",
 *   label = @Translation("Student Progress"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dh_certificate\StudentProgressListBuilder",
 *     "form" = {
 *       "default" = "Drupal\dh_certificate\Form\StudentProgressForm",
 *       "delete" = "Drupal\dh_certificate\Form\StudentProgressDeleteForm"
 *     },
 *     "access" = "Drupal\dh_certificate\StudentProgressAccessControlHandler",
 *   },
 *   base_table = "student_progress",
 *   admin_permission = "administer student progress",
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

    $fields['requirement_set'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Requirement Set'))
      ->setDescription(t('The set of requirements this progress tracks.'))
      ->setSetting('target_type', 'requirement_set')
      ->setRequired(TRUE);

    $fields['progress_data'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Progress Data'))
      ->setDescription(t('Stores progress data for each requirement.'));

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Student'))
      ->setDescription(t('The student whose progress is being tracked.'))
      ->setSetting('target_type', 'user')
      ->setRequired(TRUE);

    return $fields;
  }
}
