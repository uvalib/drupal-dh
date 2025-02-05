<?php

namespace Drupal\dh_certificate\Entity\ViewBuilder;

use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * View builder for requirements.
 */
class RequirementViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = parent::view($entity, $view_mode, $langcode);
    
    // Add requirement-specific theme suggestions
    $build['#theme'] = 'requirement';
    $build['#requirement'] = $entity;
    $build['#view_mode'] = $view_mode;
    
    // Add library for styling
    $build['#attached']['library'][] = 'dh_certificate/certificate-requirements';

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getBuildDefaults(EntityInterface $entity, $view_mode) {
    $build = parent::getBuildDefaults($entity, $view_mode);
    
    // Add cache tags for requirement set
    if ($set = $entity->getRequirementSet()) {
      $build['#cache']['tags'][] = 'requirement_set:' . $set->id();
    }

    return $build;
  }

}
