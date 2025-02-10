<?php

namespace Drupal\dh_certificate\ListBuilder;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Requirement Sets.
 */
class RequirementSetListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [
      'name' => $this->t('Name'),
      'type_counts' => $this->t('Requirements'),
      'enabled' => $this->t('Status'),
    ];
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['name'] = $entity->label();

    // Get requirement counts by type
    $type_counts = [];
    
    // Get the requirements array directly
    $requirements = $entity->requirements ?? [];
    foreach ($requirements as $requirement) {
      // Skip if not a valid requirement
      if (!$requirement) {
        continue;
      }
      
      $type = $requirement->bundle();
      if (!isset($type_counts[$type])) {
        $type_counts[$type] = 0;
      }
      $type_counts[$type]++;
    }

    // Format the counts with badges
    $counts_markup = [];
    foreach ($type_counts as $type => $count) {
      $counts_markup[] = sprintf(
        '<span class="requirement-set-list__type-count">%s: %d</span>', 
        $type, 
        $count
      );
    }

    $row['type_counts'] = [
      'data' => [
        '#markup' => !empty($counts_markup) ? implode(' ', $counts_markup) : $this->t('No requirements'),
      ],
    ];

    $row['enabled'] = [
      'data' => [
        '#markup' => $entity->status() ? $this->t('Enabled') : $this->t('Disabled'),
      ],
    ];

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function buildOperations(EntityInterface $entity) {
    $operations = parent::buildOperations($entity);
    
    // Ensure edit operation exists and has correct route
    $operations['edit'] = [
      'title' => $this->t('Edit'),
      'weight' => 10,
      'url' => $entity->toUrl('edit-form'),
    ];

    return $operations;
  }
}