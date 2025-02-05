<?php

namespace Drupal\dh_certificate\ListBuilder;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Base list builder for certificate-related entities.
 */
abstract class CertificateListBuilderBase extends EntityListBuilder {

  /**
   * Common status formatting.
   */
  protected function formatStatus($status) {
    return $status ? $this->t('Active') : $this->t('Inactive');
  }

  /**
   * Format percentage with consistent styling.
   */
  protected function formatPercentage($percentage) {
    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => $this->t('@percent%', ['@percent' => round($percentage)]),
      '#attributes' => [
        'class' => ['progress-percentage'],
      ],
    ];
  }

  /**
   * Get progress status text.
   */
  protected function getProgressStatus($percentage) {
    if ($percentage >= 100) {
      return $this->t('Complete');
    }
    elseif ($percentage > 0) {
      return $this->t('In Progress');
    }
    return $this->t('Not Started');
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = [
      '#theme' => $this->getListTheme(),
      '#items' => [],
      '#attributes' => ['class' => ['certificate-list-wrapper']],
      '#attached' => [
        'library' => ['dh_certificate/certificate-lists'],
      ],
    ];

    foreach ($this->load() as $entity) {
      $build['#items'][] = $this->buildRow($entity);
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = [];
    return $row + parent::buildRow($entity);
  }

  /**
   * Get the theme hook for the list.
   */
  protected function getListTheme() {
    return 'dh_certificate_entity_list';
  }
}
