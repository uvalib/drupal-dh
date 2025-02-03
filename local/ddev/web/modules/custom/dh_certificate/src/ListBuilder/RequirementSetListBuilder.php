<?php

namespace Drupal\dh_certificate\ListBuilder;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Requirement Set entities.
 */
class RequirementSetListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\dh_certificate\Entity\RequirementSetConfig $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['status'] = $entity->status() ? $this->t('Enabled') : $this->t('Disabled');
    
    // Get count of requirements
    $requirements = $entity->getRequirements();
    $row['requirements'] = [
      'data' => [
        '#markup' => $this->formatPlural(
          count($requirements),
          '1 requirement',
          '@count requirements'
        ),
      ],
    ];

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    if ($entity->status()) {
      $operations['disable'] = [
        'title' => $this->t('Disable'),
        'url' => $entity->toUrl('disable'),
        'weight' => 50,
      ];
    }
    else {
      $operations['enable'] = [
        'title' => $this->t('Enable'),
        'url' => $entity->toUrl('enable'),
        'weight' => 50,
      ];
    }

    return $operations;
  }

}