<?php

namespace Drupal\dh_certificate\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Requirement Type annotation object.
 *
 * @Annotation
 */
class RequirementType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the requirement type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The description of the requirement type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * The weight of the requirement type.
   *
   * @var int
   */
  public $weight = 0;

}
