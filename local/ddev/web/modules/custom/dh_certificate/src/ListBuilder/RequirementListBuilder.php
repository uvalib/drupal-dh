<?php

namespace Drupal\dh_certificate\ListBuilder;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a listing of Certificate Requirements.
 */
class RequirementListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Requirement');
    $header['type'] = $this->t('Type');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\dh_certificate\Entity\Requirement $entity */
    $row['label'] = $entity->label();
    $row['type'] = $entity->getType();
    $row['status'] = $entity->isEnabled() ? $this->t('Enabled') : $this->t('Disabled');
    return $row + parent::buildRow($entity);
  }
}