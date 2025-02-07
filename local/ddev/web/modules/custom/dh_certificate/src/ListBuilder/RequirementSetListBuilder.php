<?php

namespace Drupal\dh_certificate\ListBuilder;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Provides a listing of Requirement Set entities.
 */
class RequirementSetListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Name');
    $header['id'] = $this->t('Machine name');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\dh_certificate\Entity\RequirementSet $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['status'] = $entity->isEnabled() ? $this->t('Enabled') : $this->t('Disabled');
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    
    if ($entity->isEnabled()) {
      $operations['disable'] = [
        'title' => $this->t('Disable'),
        'url' => Url::fromRoute('entity.requirement_set.disable', ['requirement_set' => $entity->id()]),
        'weight' => 50,
      ];
    }
    else {
      $operations['enable'] = [
        'title' => $this->t('Enable'),
        'url' => Url::fromRoute('entity.requirement_set.enable', ['requirement_set' => $entity->id()]),
        'weight' => 50,
      ];
    }

    return $operations;
  }

}