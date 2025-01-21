<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;
use Drupal\Component\Utility\Xss;

/**
 * Provides an Events block for the DH Dashboard.
 *
 * @Block(
 *   id = "dh_dashboard_events",
 *   admin_label = @Translation("DH Dashboard Events"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DHEventsBlock extends DHDashboardBlockBase {

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
    return 'event';
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultSortField(): string {
    return 'field_date_range1';  // Updated to use the correct field from event content type
  }

  /**
   * {@inheritdoc}
   */
  protected function getBlockClass(): string {
    return 'dh-dashboard-events';
  }

  /**
   * {@inheritdoc}
   */
  protected function getItemsPerPageConfigKey(): string {
    return 'events_items_per_page';
  }

  /**
   * {@inheritdoc}
   */
  protected function getDisplayModeConfigKey(): string {
    return 'events_display_mode';
  }

  /**
   * {@inheritdoc}
   */
  protected function transformEntity($entity) {
    // Get event date
    $event_date = NULL;
    if ($entity->hasField('field_date_range1') && !$entity->get('field_date_range1')->isEmpty()) {
      $event_date = $entity->get('field_date_range1')->first()->get('value')->getString();
    }

    // Get event location and standardize line breaks
    $location = '';
    if ($entity->hasField('field_event_location') && !$entity->get('field_event_location')->isEmpty()) {
      $location = $entity->get('field_event_location')->value;
      // Convert </p> to line breaks first
      $location = preg_replace('/<\/p>/', '<br>', $location);
      // Convert <p> tags to line breaks
      $location = preg_replace('/<p[^>]*>/', '', $location);
      // Replace other closing tags with line breaks
      $location = preg_replace('/<\/(div|span)>/', '<br>', $location);
      // Remove other HTML tags but preserve <br>
      $location = strip_tags($location, '<br>');
      // Normalize <br> tags and clean up
      $location = str_replace(['<br/>', '<br />'], '<br>', $location);
      $location = preg_replace('/(<br>[\s]*){2,}/', '<br>', $location);
      $location = trim($location);
    }

    // Get event type with link
    $type = '';
    $type_url = '';
    if ($entity->hasField('field_event_type') && !$entity->get('field_event_type')->isEmpty()) {
      $type_entity = $entity->get('field_event_type')->entity;
      if ($type_entity) {
        $type = $type_entity->label();
        $type_url = $type_entity->toUrl()->toString();
      }
    }

    // Get department/school with link
    $department = '';
    $department_url = '';
    if ($entity->hasField('field_department_or_school') && !$entity->get('field_department_or_school')->isEmpty()) {
      $dept_entity = $entity->get('field_department_or_school')->entity;
      if ($dept_entity) {
        $department = $dept_entity->label();
        $department_url = $dept_entity->toUrl()->toString();
      }
    }

    // Get image
    $image_url = '';
    if ($entity->hasField('field_image') && !$entity->get('field_image')->isEmpty()) {
      $image = $entity->get('field_image')->entity;
      if ($image) {
        $image_url = $image->createFileUrl();
      }
    }

    // Get more information link
    $more_info_url = '';
    if ($entity->hasField('field_link_to_more_information') && !$entity->get('field_link_to_more_information')->isEmpty()) {
      $more_info_url = $entity->get('field_link_to_more_information')->first()->getUrl()->toString();
    }

    // Get online meeting link
    $meeting_url = '';
    if ($entity->hasField('field_link_to_online_meeting') && !$entity->get('field_link_to_online_meeting')->isEmpty()) {
      $meeting_url = $entity->get('field_link_to_online_meeting')->first()->getUrl()->toString();
    }

    // Get full body text
    $body = '';
    if ($entity->hasField('body') && !$entity->get('body')->isEmpty()) {
      $body = $entity->get('body')->value;
      // Strip any malicious HTML but preserve basic formatting
      $body = Xss::filter($body, ['p', 'br', 'strong', 'em', 'a', 'ul', 'ol', 'li']);
    }

    return [
      'title' => $entity->label(),
      'url' => $entity->toUrl()->toString(),
      'date' => $event_date,
      'location' => $location,
      'type' => [
        'label' => $type,
        'url' => $type_url,
      ],
      'department' => [
        'label' => $department,
        'url' => $department_url,
      ],
      'summary' => $entity->hasField('body') ? $entity->get('body')->summary : '',
      'body' => $body,
      'image_url' => $image_url,
      'more_info_url' => $more_info_url,
      'meeting_url' => $meeting_url,
      'category_class' => 'event-category--' . strtolower($type),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function addQueryConditions($query) {
    $config = $this->getConfiguration();
    
    // Base conditions
    $query->condition('status', 1)
      ->accessCheck(TRUE);

    // Event type filter
    if (!empty($config['event_type_filter']) && $config['event_type_filter'] !== 'all') {
      $query->condition('field_event_type.entity.name', $config['event_type_filter']);
    }

    // Date filter
    if (empty($config['show_past_events'])) {
      $now = new \DateTime();
      $query->condition('field_date_range1.value', $now->format('Y-m-d\TH:i:s'), '>=');
    }

    // Sort by event date
    $query->sort('field_date_range1.value', 'ASC');
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $config = \Drupal::config('dh_dashboard.settings');
    return [
      'event_type_filter' => 'all',
      'show_past_events' => TRUE,
      'items_per_page' => $config->get('default_items_per_page') ?? 3,
      'display_mode' => 'grid',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['event_type_filter'] = [
      '#type' => 'select',
      '#title' => $this->t('Event type filter'),
      '#default_value' => $config['event_type_filter'],
      '#options' => [
        'all' => $this->t('All types'),
        'conference' => $this->t('Conferences'),
        'workshop' => $this->t('Workshops'),
        'seminar' => $this->t('Seminars'),
      ],
    ];

    $form['show_past_events'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show past events'),
      '#default_value' => $config['show_past_events'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['event_type_filter'] = $form_state->getValue('event_type_filter');
    $this->configuration['show_past_events'] = $form_state->getValue('show_past_events');
  }

  /**
   * {@inheritdoc}
   */
  protected function getItems(): array {
    $config = $this->getConfiguration();
    
    // Get items using parent method which uses entity queries
    $items = parent::getItems();
    
    // Attach library using the correct name
    $items['#attached']['library'][] = 'dh_dashboard/event_preview';
    
    // Add debug to verify data
    $items['#attached']['drupalSettings']['dhDashboard'] = [
      'debug' => TRUE
    ];
    
    // Add our custom classes
    $items['attributes'] = [
      'class' => ['dh-events-block', 'events-grid', 'block-spacing'],
    ];

    return $items;
  }

  /**
   * {@inheritdoc}
   */
  protected function getThemeId(): string {
    return 'dh_dashboard_events';
  }

  /**
   * {@inheritdoc}
   */
  protected function getItemType(): string {
    return 'event';
  }
}






