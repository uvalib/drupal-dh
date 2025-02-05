<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;

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
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "requirement_set",
 *   admin_permission = "administer certificate requirements",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "status"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "requirements",
 *     "weight",
 *     "status"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/requirement_set/{requirement_set}",
 *     "add-form" = "/admin/structure/requirement_set/add",
 *     "edit-form" = "/admin/structure/requirement_set/{requirement_set}/edit",
 *     "delete-form" = "/admin/structure/requirement_set/{requirement_set}/delete",
 *     "collection" = "/admin/structure/requirement_set"
 *   }
 * )
 */
class RequirementSet extends ConfigEntityBase implements RequirementSetInterface {

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
   * The Requirement Set description.
   *
   * @var string
   */
  protected $description = '';

  /**
   * The requirements in this set.
   *
   * @var array
   */
  protected $requirements = [];

  /**
   * The weight of this requirement set.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * The status of this requirement set.
   *
   * @var bool
   */
  protected $status = TRUE;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequirements() {
    return $this->requirements;
  }

  /**
   * {@inheritdoc}
   */
  public function setRequirements(array $requirements) {
    $this->requirements = $requirements;
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

  /**
   * {@inheritdoc}
   */
  public function isEnabled() {
    return (bool) $this->status;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->status = $status;
    return $this;
  }
}