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
 *     "list_builder" = "Drupal\dh_certificate\RequirementSetListBuilder",
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
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "requirements",
 *     "workflows"
 *   },
 *   links = {
 *     "collection" = "/admin/config/dh_certificate/requirement-sets",
 *     "add-form" = "/admin/config/dh_certificate/requirement-sets/add",
 *     "edit-form" = "/admin/config/dh_certificate/requirement-sets/{requirement_set}/edit",
 *     "delete-form" = "/admin/config/dh_certificate/requirement-sets/{requirement_set}/delete"
 *   }
 * )
 */
class RequirementSet extends ConfigEntityBase {
  // ...existing code...
}
