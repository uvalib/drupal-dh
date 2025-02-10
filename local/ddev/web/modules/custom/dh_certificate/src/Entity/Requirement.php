<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Requirement entity.
 *
 * @ConfigEntityType(
 *   id = "requirement",
 *   label = @Translation("Requirement"),
 *   handlers = {
 *     "list_builder" = "Drupal\dh_certificate\ListBuilder\RequirementListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dh_certificate\Form\RequirementForm",
 *       "edit" = "Drupal\dh_certificate\Form\RequirementForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   config_prefix = "requirement",
 *   admin_permission = "administer dh certificate requirements",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status",
 *     "type" = "type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "type",
 *     "settings",
 *     "status"
 *   }
 * )
 */
class Requirement extends ConfigEntityBase {

  /**
   * The Requirement ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Requirement label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Requirement type.
   *
   * @var string
   */
  protected $type;

  /**
   * The Requirement settings.
   *
   * @var array
   */
  protected $settings = [];

  /**
   * The Requirement status.
   *
   * @var bool
   */
  protected $status = TRUE;

  /**
   * Gets the type.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Updates the type directly.
   */
  public function updateType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * Gets settings.
   *
   * @return array
   *   The settings array.
   */
  public function getSettings() {
    return $this->settings ?? [];
  }

  /**
   * Sets a specific setting value.
   *
   * @param string $key
   *   The setting key.
   * @param mixed $value
   *   The setting value.
   *
   * @return $this
   */
  public function setSetting($key, $value) {
    $this->settings[$key] = $value;
    return $this;
  }

  /**
   * Updates settings directly.
   */
  public function updateSettings(array $settings) {
    $this->settings = $settings;
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

}
