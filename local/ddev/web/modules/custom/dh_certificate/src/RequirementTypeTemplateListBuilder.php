<?php

namespace Drupal\dh_certificate;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Requirement Type Templates.
 */
class RequirementTypeTemplateListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Template Name');
    $header['type'] = $this->t('Type');
    $header['id'] = $this->t('Machine name');
    $header['weight'] = $this->t('Weight');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\dh_certificate\RequirementTypeTemplateInterface $entity */
    $row['label'] = $entity->label();
    $row['type'] = $entity->getType();
    $row['id'] = $entity->id();
    $row['weight'] = $entity->getWeight();
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    
    if (isset($operations['edit'])) {
      $operations['edit']['title'] = $this->t('Edit');
    }

    return $operations;
  }

}
