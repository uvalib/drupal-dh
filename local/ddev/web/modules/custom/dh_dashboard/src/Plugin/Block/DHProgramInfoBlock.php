<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a DH Program Information Block.
 *
 * @Block(
 *   id = "dh_dashboard_program_info",
 *   admin_label = @Translation("Program Information"),
 *   category = @Translation("DH Dashboard"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user", required = FALSE)
 *   }
 * )
 */
class DHProgramInfoBlock extends DHDashboardBlockBase {

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
    return 'program';
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
  protected function getThemeHook(): string {
    return 'dh_dashboard_program_info';
  }

  /**
   * {@inheritdoc}
   */
  protected function getThemeId(): string {
    return 'dh_dashboard_program_info';
  }

  /**
   * {@inheritdoc}
   */
  protected function getItemType(): string {
    return 'program';
  }

  /**
   * {@inheritdoc}
   */
  protected function getBlockClass(): string {
    return 'dh-dashboard-program-info';
  }

  /**
   * {@inheritdoc}
   */
  protected function getItemsPerPageConfigKey(): string {
    return 'program_items_per_page';
  }

  /**
   * {@inheritdoc}
   */
  protected function getDisplayModeConfigKey(): string {
    return 'program_display_mode';
  }

  /**
   * {@inheritdoc}
   */
  protected function transformEntity($entity) {
    return [
      'title' => $entity->label(),
      'url' => $entity->toUrl()->toString(),
      'date' => $entity->get('created')->value,
      'description' => $entity->hasField('body') ? 
        $entity->get('body')->summary : '',
      'requirements' => $entity->hasField('field_requirements') ? 
        $entity->get('field_requirements')->value : '',
      'status' => $entity->hasField('field_status') ? 
        $entity->get('field_status')->value : '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function addQueryConditions($query) {
    $query->condition('status', 1)
      ->accessCheck(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'show_dates' => TRUE,
      'show_resources' => TRUE,
      'show_requirements' => TRUE,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    
    // Get the items
    $items = $this->getItems();
    
    // Add program info specific data
    $build['#program_info'] = $items;
    
    // Add standard dashboard block classes
    $build['#attributes']['class'][] = 'dh-dashboard-block';
    $build['#attributes']['class'][] = 'dh-dashboard-block--program-info';
    
    // Debug if enabled
    if ($this->configuration['show_debug']) {
      \Drupal::logger('dh_dashboard')->debug('Program Info Block Data: @data', [
        '@data' => print_r($items, TRUE),
      ]);
    }
    
    return $build;
  }

  protected function getItems(): array {
    $config = $this->getConfiguration();
    $items = ['items' => []];

    if ($config['show_dates'] ?? TRUE) {
      $items['items']['important_dates'] = [
        [
          'title' => 'Application Deadline (Fall 2024)',
          'date' => '2024-04-15',
          'description' => 'Last day to submit certificate program applications',
          'icon' => 'calendar',
          'class' => 'program-info-card date-item--deadline',
        ],
        [
          'title' => 'Summer Institute Registration',
          'date' => '2024-03-01',
          'description' => 'Registration opens for DH Summer Institute workshops',
          'icon' => 'calendar',
          'class' => 'program-info-card date-item--registration',
        ],
        [
          'title' => 'Capstone Submission Deadline',
          'date' => '2024-05-10',
          'description' => 'Final day to submit capstone projects for Spring graduation',
          'icon' => 'calendar',
          'class' => 'program-info-card date-item--submission',
        ],
      ];
    }

    if ($config['show_resources'] ?? TRUE) {
      $items['items']['resources'] = [
        [
          'title' => 'Program Handbook',
          'url' => '/dh-certificate/handbook',
          'description' => 'Complete guide to program requirements and policies',
          'type' => 'document',
          'icon' => 'book',
          'class' => 'program-info-card resource-item--document',
        ],
        [
          'title' => 'Course Schedule',
          'url' => '/dh-certificate/courses',
          'description' => 'Browse upcoming DH certificate courses',
          'type' => 'schedule',
          'icon' => 'calendar',
          'class' => 'program-info-card resource-item--schedule',
        ],
        [
          'title' => 'Digital Tools Guide',
          'url' => '/dh-certificate/tools',
          'description' => 'Resources for recommended software and platforms',
          'type' => 'guide',
          'icon' => 'tools',
          'class' => 'program-info-card resource-item--guide',
        ],
        [
          'title' => 'Academic Advising',
          'url' => '/dh-certificate/advising',
          'description' => 'Schedule a meeting with program advisors',
          'type' => 'contact',
          'icon' => 'user',
          'class' => 'program-info-card resource-item--contact',
        ],
      ];
    }

    if ($config['show_requirements'] ?? TRUE) {
      $items['items']['requirements_summary'] = [
        'total_credits' => 15,
        'core_courses' => 4,
        'electives' => 2,
        'capstone' => 1,
        'time_limit' => '2 years',
      ];
    }

    $items['attributes'] = [
      'class' => ['dh-program-info', 'info-grid', 'block-spacing', 'dh-dashboard-content'],
    ];

    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['show_dates'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show important dates'),
      '#default_value' => $config['show_dates'] ?? TRUE,
    ];

    $form['show_resources'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show resources'),
      '#default_value' => $config['show_resources'] ?? TRUE,
    ];

    $form['show_requirements'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show requirements summary'),
      '#default_value' => $config['show_requirements'] ?? TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['show_dates'] = $form_state->getValue('show_dates');
    $this->configuration['show_resources'] = $form_state->getValue('show_resources');
    $this->configuration['show_requirements'] = $form_state->getValue('show_requirements');
  }
}
