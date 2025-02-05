<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Requirement entity.
 *
 * @ContentEntityType(
 *   id = "requirement",
 *   label = @Translation("Certificate Requirement"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dh_certificate\ListBuilder\RequirementListBuilder",
 *     "form" = {
 *       "default" = "Drupal\dh_certificate\Form\RequirementForm",
 *       "add" = "Drupal\dh_certificate\Form\RequirementForm",
 *       "edit" = "Drupal\dh_certificate\Form\RequirementForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "certificate_requirement",
 *   admin_permission = "administer certificate requirements",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/requirement/{requirement}",
 *     "add-form" = "/admin/structure/requirement/add",
 *     "edit-form" = "/admin/structure/requirement/{requirement}/edit",
 *     "delete-form" = "/admin/structure/requirement/{requirement}/delete",
 *     "collection" = "/admin/structure/requirement",
 *   }
 * )
 */
class Requirement extends ContentEntityBase implements RequirementInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Type'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['requirement_set'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Requirement Set'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'requirement_set')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['weight'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Weight'))
      ->setDefaultValue(0)
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }
}
