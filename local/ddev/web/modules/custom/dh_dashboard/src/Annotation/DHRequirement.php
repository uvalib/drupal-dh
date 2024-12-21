<?php

namespace Drupal\dh_dashboard\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a DH Requirement annotation object.
 *
 * @Annotation
 */
class DHRequirement extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the requirement.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The type of requirement (course or general).
   *
   * @var string
   */
  public $type;
}