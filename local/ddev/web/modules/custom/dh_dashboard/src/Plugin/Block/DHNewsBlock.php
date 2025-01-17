<?php

namespace Drupal\dh_dashboard\Plugin\Block;

/**
 * Provides a News block for the DH Dashboard.
 *
 * @Block(
 *   id = "dh_dashboard_news",
 *   admin_label = @Translation("DH Dashboard News"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DHNewsBlock extends DHDashboardBlockBase {

  /**
   * {@inheritdoc}
   */
  protected function getAvailableEntityTypes(): array {
    return [
      'node' => $this->t('Content'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultEntityType(): string {
    return 'node';
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultBundle(): string {
    return 'news';
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultSortField(): string {
    return 'created';
  }

  /**
   * {@inheritdoc}
   */
  protected function getBlockClass(): string {
    return 'dh-dashboard-news';
  }

  /**
   * {@inheritdoc}
   */
  protected function getItemsPerPageConfigKey(): string {
    return 'news_items_per_page';
  }

  /**
   * {@inheritdoc}
   */
  protected function getDisplayModeConfigKey(): string {
    return 'news_display_mode';
  }

  /**
   * {@inheritdoc}
   */
  protected function transformEntity($entity) {
    $image_url = '';
    if ($entity->hasField('field_image') && !$entity->get('field_image')->isEmpty()) {
      $image = $entity->get('field_image')->entity;
      if ($image) {
        $image_url = $image->createFileUrl();
      }
    }

    return [
      'title' => $entity->label(),
      'url' => $entity->toUrl()->toString(),
      'date' => $entity->get('created')->value,
      'summary' => $entity->hasField('body') ? 
        $entity->get('body')->summary : '',
      'image_url' => $image_url,
      'author' => $entity->getOwner()->getDisplayName(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function addQueryConditions($query) {
    // Add any news-specific conditions
    $query->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('sticky', 'DESC'); // Show sticky news items first
  }

  /**
   * {@inheritdoc}
   */
  protected function getThemeId(): string {
    return 'dh_dashboard_news';
  }

  /**
   * {@inheritdoc}
   */
  protected function getItemType(): string {
    return 'news';
  }

}