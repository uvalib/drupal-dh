<?php

namespace Drupal\dh_certificate\ListBuilder;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Requirement Type Template entities.
 */
class RequirementTypeTemplateListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['type'] = $this->t('Type');
    $header['weight'] = $this->t('Weight');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\dh_certificate\Entity\RequirementTypeTemplate $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['type'] = $entity->getType();
    $row['weight'] = $entity->getWeight();
    return $row + parent::buildRow($entity);
  }

}