<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Requirement Type entity.
 *
 * @ConfigEntityType(
 *   id = "requirement_type",
 *   label = @Translation("Requirement Type"),
 *   handlers = {
 *     "list_builder" = "Drupal\dh_certificate\RequirementTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dh_certificate\Form\RequirementTypeForm",
 *       "edit" = "Drupal\dh_certificate\Form\RequirementTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "requirement_type",
 *   admin_permission = "administer dh certificate requirements",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status",
 *     "weight" = "weight",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "weight",
 *     "status"
 *   },
 *   links = {
 *     "collection" = "/admin/config/dh-certificate/requirements/types",
 *     "add-form" = "/admin/config/dh-certificate/requirements/types/add",
 *     "edit-form" = "/admin/config/dh-certificate/requirements/types/{requirement_type}/edit",
 *     "delete-form" = "/admin/config/dh-certificate/requirements/types/{requirement_type}/delete"
 *   }
 * )
 */
class RequirementType extends ConfigEntityBase {

  /**
   * The Requirement Type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Requirement Type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Requirement Type description.
   *
   * @var string
   */
  protected $description;

  /**
   * The Requirement Type weight.
   *
   * @var int
   */
  protected $weight = 0;

}
