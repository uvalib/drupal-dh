<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Requirement Set configuration entity.
 *
 * @ConfigEntityType(
 *   id = "requirement_set",
 *   label = @Translation("Requirement Set"),
 *   handlers = {
 *     "list_builder" = "Drupal\dh_certificate\ListBuilder\RequirementSetListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dh_certificate\Form\RequirementSetForm",
 *       "edit" = "Drupal\dh_certificate\Form\RequirementSetForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "requirement_set",
 *   admin_permission = "administer requirement sets",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status"
 *   },
 *   links = {
 *     "collection" = "/admin/config/dh-certificate/requirement-sets",
 *     "add-form" = "/admin/config/dh-certificate/requirement-sets/add",
 *     "edit-form" = "/admin/config/dh-certificate/requirement-sets/{requirement_set}/edit",
 *     "delete-form" = "/admin/config/dh-certificate/requirement-sets/{requirement_set}/delete",
 *     "activate" = "/admin/config/dh-certificate/requirement-sets/{requirement_set}/activate",
 *     "deactivate" = "/admin/config/dh-certificate/requirement-sets/{requirement_set}/deactivate"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "status",
 *     "requirements"
 *   }
 * )
 */
class RequirementSetConfig extends ConfigEntityBase {

  /**
   * The Requirement Set ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Requirement Set label.
   *
   * @var string
   */
  protected $label;

  /**
   * The requirements.
   *
   * @var array
   */
  protected $requirements = [];

  /**
   * Gets the requirements.
   *
   * @return array
   *   The requirements.
   */
  public function getRequirements() {
    return $this->requirements;
  }

}
