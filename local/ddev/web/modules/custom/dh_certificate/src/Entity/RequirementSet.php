<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Requirement Set entity.
 *
 * @ConfigEntityType(
 *   id = "requirement_set",
 *   label = @Translation("Requirement Set"),
 *   handlers = {
 *     "list_builder" = "Drupal\dh_certificate\ListBuilder\RequirementSetListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dh_certificate\Form\RequirementSetForm",
 *       "edit" = "Drupal\dh_certificate\Form\RequirementSetForm",
 *       "delete" = "Drupal\dh_certificate\Form\RequirementSetDeleteForm"
 *     }
 *   },
 *   config_prefix = "requirement_set",
 *   admin_permission = "administer certificate requirements",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "requirements",
 *     "status"
 *   },
 *   links = {
 *     "collection" = "/admin/config/dh_certificate/requirement-sets",
 *     "add-form" = "/admin/config/dh_certificate/requirement-sets/add",
 *     "edit-form" = "/admin/config/dh_certificate/requirement-sets/{requirement_set}",
 *     "delete-form" = "/admin/config/dh_certificate/requirement-sets/{requirement_set}/delete",
 *     "enable" = "/admin/config/dh_certificate/requirement-sets/{requirement_set}/enable",
 *     "disable" = "/admin/config/dh_certificate/requirement-sets/{requirement_set}/disable"
 *   }
 * )
 */
class RequirementSet extends ConfigEntityBase {

  /**
   * The RequirementSet ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The RequirementSet label.
   *
   * @var string
   */
  protected $label;

  /**
   * The requirements configuration.
   *
   * @var array
   */
  protected $requirements = [];

  /**
   * Whether the requirement set is enabled.
   *
   * @var bool
   */
  protected $status = TRUE;

  /**
   * Gets the requirements.
   *
   * @return array
   *   The requirements configuration.
   */
  public function getRequirements() {
    return $this->requirements;
  }

  /**
   * Sets the requirements.
   *
   * @param array $requirements
   *   The requirements configuration.
   *
   * @return $this
   */
  public function setRequirements(array $requirements) {
    $this->requirements = $requirements;
    return $this;
  }

  /**
   * Adds a requirement.
   *
   * @param string $type
   *   The requirement type.
   * @param array $config
   *   The requirement configuration.
   *
   * @return $this
   */
  public function addRequirement($type, array $config) {
    if (!isset($this->requirements[$type])) {
      $this->requirements[$type] = [];
    }
    $this->requirements[$type][] = $config;
    return $this;
  }

  /**
   * Validates requirements configuration.
   *
   * @param array $requirements
   *   The requirements configuration to validate.
   *
   * @return array
   *   Array of validation errors, empty if valid.
   */
  public function validateRequirements(array $requirements) {
    $errors = [];
    
    foreach ($requirements as $type => $config) {
      try {
        if (!$this->requirementTypeManager->hasDefinition($type)) {
          $errors[] = $this->t('Unknown requirement type: @type', ['@type' => $type]);
          continue;
        }

        $requirement = $this->requirementTypeManager->getRequirementType($type);
        if (!$requirement->validateConfiguration($config)) {
          $errors[] = $this->t('Invalid configuration for requirement type: @type', ['@type' => $type]);
        }
      }
      catch (\Exception $e) {
        $errors[] = $e->getMessage();
      }
    }
    
    return $errors;
  }
}
