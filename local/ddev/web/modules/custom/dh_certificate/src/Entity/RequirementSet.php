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
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   config_prefix = "requirement_set",
 *   admin_permission = "administer dh certificate requirements",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status"
 *   },
 *   links = {
 *     "collection" = "/admin/config/dh-certificate/requirements/sets",
 *     "add-form" = "/admin/config/dh-certificate/requirements/sets/add",
 *     "edit-form" = "/admin/config/dh-certificate/requirements/sets/{requirement_set}/edit",
 *     "delete-form" = "/admin/config/dh-certificate/requirements/sets/{requirement_set}/delete",
 *     "enable" = "/admin/config/dh-certificate/requirements/sets/{requirement_set}/enable",
 *     "disable" = "/admin/config/dh-certificate/requirements/sets/{requirement_set}/disable"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "requirements",
 *     "status"
 *   }
 * )
 */
class RequirementSet extends ConfigEntityBase {

  protected $id;
  protected $label;
  protected $description;
  protected $requirements = [];
  protected $status = TRUE;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->label;
  }

  /**
   * Gets the description.
   */
  public function getDescription() {
    return $this->description ?? '';
  }

  /**
   * Updates description directly.
   */
  public function updateDescription($description) {
    $this->description = $description;
    return $this;
  }

  /**
   * Gets requirements.
   */
  public function getRequirements() {
    return $this->requirements ?? [];
  }

  /**
   * Updates requirements directly.
   */
  public function updateRequirements(array $requirements) {
    $this->requirements = $requirements;
    return $this;
  }

  /**
   * Gets enabled status.
   */
  public function isEnabled() {
    return (bool) $this->status;
  }

  /**
   * Updates enabled status directly.
   */
  public function updateStatus($status) {
    $this->status = (bool) $status;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  protected function invalidateTagsOnSave($update) {
    parent::invalidateTagsOnSave($update);
    if ($update) {
      \Drupal::service('cache_tags.invalidator')->invalidateTags(['requirement_set_list']);
    }
  }

}