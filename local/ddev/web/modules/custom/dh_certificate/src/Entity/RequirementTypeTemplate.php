<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\dh_certificate\RequirementTypeTemplateInterface;

/**
 * Defines the Requirement Type Template entity.
 *
 * @ConfigEntityType(
 *   id = "requirement_type_template",
 *   label = @Translation("Requirement Type Template"),
 *   handlers = {
 *     "list_builder" = "Drupal\dh_certificate\RequirementTypeTemplateListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dh_certificate\Form\RequirementTypeTemplateForm",
 *       "edit" = "Drupal\dh_certificate\Form\RequirementTypeTemplateForm",
 *       "delete" = "Drupal\dh_certificate\Form\RequirementTypeTemplateDeleteForm"
 *     }
 *   },
 *   config_prefix = "requirement_type_template",
 *   admin_permission = "administer dh certificate",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "weight" = "weight"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "type",
 *     "config",
 *     "weight"
 *   },
 *   links = {
 *     "collection" = "/admin/config/dh-certificate/requirement-templates",
 *     "add-form" = "/admin/config/dh-certificate/requirement-templates/add",
 *     "edit-form" = "/admin/config/dh-certificate/requirement-templates/{requirement_type_template}/edit",
 *     "delete-form" = "/admin/config/dh-certificate/requirement-templates/{requirement_type_template}/delete"
 *   }
 * )
 */
class RequirementTypeTemplate extends ConfigEntityBase implements RequirementTypeTemplateInterface {

  /**
   * The Requirement Type Template ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Requirement Type Template label.
   *
   * @var string
   */
  protected $label;

  /**
   * The requirement type.
   *
   * @var string
   */
  protected $type;

  /**
   * The requirement configuration.
   *
   * @var array
   */
  protected $config;

  /**
   * The weight of this template in relation to others.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfig(array $config) {
    $this->config = $config;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->weight = $weight;
    return $this;
  }

}
