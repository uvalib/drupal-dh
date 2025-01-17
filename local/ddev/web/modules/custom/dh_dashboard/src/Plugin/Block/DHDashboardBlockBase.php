<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Url;

/**
 * Provides a base block for the Digital Humanities Dashboard.
 */
abstract class DHDashboardBlockBase extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new DHDashboardBlockBase.
   *
   * @param array $configuration
   *   A configuration array.
   * @param string $plugin_id
   *   The plugin_id.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory,
    AccountInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    // Get default configuration from settings
    $default_items_per_page = $config_factory->get('dh_dashboard.settings')->get('default_items_per_page') ?? 3;
    
    // Merge with provided configuration
    $configuration += [
      'items_per_page' => $default_items_per_page,
      'display_mode' => 'grid',
      'show_debug' => FALSE,
      'category_filter' => 'all',
      'use_compact_mode' => FALSE,
    ];
    
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'items_per_page' => 3,
      'display_mode' => 'grid',
      'show_debug' => FALSE,
      'entity_type' => $this->getDefaultEntityType(),
      'bundle' => $this->getDefaultBundle(),
      'sort_field' => $this->getDefaultSortField(),
      'sort_direction' => 'DESC',
      'filter_criteria' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    // Entity type selection
    $entity_types = $this->getAvailableEntityTypes();
    $form['entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Entity type'),
      '#options' => $entity_types,
      '#default_value' => $config['entity_type'],
      '#ajax' => [
        'callback' => [$this, 'updateBundleOptions'],
        'wrapper' => 'bundle-wrapper',
      ],
    ];

    // Bundle selection
    $form['bundle_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'bundle-wrapper'],
    ];

    $selected_entity_type = $form_state->getValue(['settings', 'entity_type']) ?? $config['entity_type'];
    $bundles = $this->getAvailableBundles($selected_entity_type);
    
    $form['bundle_wrapper']['bundle'] = [
      '#type' => 'select',
      '#title' => $this->t('Bundle'),
      '#options' => $bundles,
      '#default_value' => $config['bundle'],
    ];

    // Sort field selection
    $sort_fields = $this->getAvailableSortFields($selected_entity_type, $config['bundle']);
    $form['sort_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Sort by'),
      '#options' => $sort_fields,
      '#default_value' => $config['sort_field'],
    ];

    $form['sort_direction'] = [
      '#type' => 'select',
      '#title' => $this->t('Sort direction'),
      '#options' => [
        'ASC' => $this->t('Ascending'),
        'DESC' => $this->t('Descending'),
      ],
      '#default_value' => $config['sort_direction'],
    ];

    $form['items_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Items per page'),
      '#default_value' => $config['items_per_page'] ?? 3,
      '#min' => 1,
    ];

    return $form;
  }

  /**
   * Ajax callback to update bundle options.
   */
  public function updateBundleOptions(array &$form, FormStateInterface $form_state) {
    return $form['settings']['bundle_wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['items_per_page'] = $form_state->getValue('items_per_page');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the module's debug setting
    $module_config = \Drupal::config('dh_dashboard.settings');
    $show_debug = $module_config->get('show_debug') ?: FALSE;

    // Get current page from URL query parameter with bounds checking
    $current_page = max(0, (int) (\Drupal::request()->query->get('page') ?: 0));
    
    // Get all items first
    $all_items = $this->getItems();
    $total_items = count($all_items['items']);
    
    // Get page size from configuration
    $page_size = (int) ($this->configuration['items_per_page']);
    
    // Use module's debug setting for messages
    if ($show_debug) {
      \Drupal::messenger()->addMessage('Configuration dump: ' . print_r($this->configuration, TRUE));
    }
    
    // Calculate pagination values
    $total_pages = max(1, ceil($total_items / $page_size));
    $current_page = min($current_page, max(0, $total_pages - 1));
    $start = $current_page * $page_size;
    
    // Create pager data
    $pager_data = [
      'total' => $total_items,
      'per_page' => $page_size,
      'current_page' => $current_page,
      'total_pages' => $total_pages,
    ];

    // Use module's debug setting for messages
    if ($show_debug) {
      \Drupal::messenger()->addMessage('Pager data before build: ' . print_r($pager_data, TRUE));
    }
    
    // Slice the items after pager data is created
    if (isset($all_items['items']) && is_array($all_items['items'])) {
      $all_items['items'] = array_values($all_items['items']);
      $all_items['items'] = array_slice($all_items['items'], $start, $page_size);
    }

    // Get the block instance ID - use plugin ID if no config ID is available
    $block_id = $this->getConfiguration()['id'] ?? $this->getPluginId();

    // Create the build array properly
    $build = [
      '#theme' => $this->getThemeId(),
      '#items' => $all_items,
      '#show_debug' => $show_debug, // Use module's debug setting
      '#items_per_page' => $page_size,
      '#pager_data' => $pager_data,
      '#block_id' => $block_id, // Pass the block ID to template
      '#attributes' => new Attribute([
        'class' => ['dh-dashboard-block', 'block-spacing'],
        'id' => 'block-' . str_replace('_', '-', $block_id), // Use consistent ID format
      ]),
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.query_args:page'],
        'tags' => ['config:dh_dashboard.settings'], // Add config tag for debug setting
      ],
      '#attached' => [
        'library' => ['dh_dashboard/dashboard'],
        'drupalSettings' => [
          'dhDashboard' => [
            'items' => [
              'totalItems' => $total_items,
              'itemsPerPage' => $page_size,
              'currentPage' => $current_page,
            ],
            'pager' => [
              // Use \Drupal\Core\Url to get an absolute or relative path:
              'baseUrl' => Url::fromRoute('dh_dashboard.main')->toString(),
            ],
          ],
        ],
      ],
    ];

    // Add pager data if this block uses pagination
    if (isset($build['#pager_data'])) {
      $build['#pager_data']['block_id'] = $this->getPluginId();
    }
    
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['user']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['dh_dashboard:blocks']);
  }

  /**
   * Gets the block-specific CSS class.
   *
   * @return string
   *   The CSS class name.
   */
  abstract protected function getBlockClass(): string;

  /**
   * Gets the config key for items per page setting.
   *
   * @return string
   *   The config key.
   */
  abstract protected function getItemsPerPageConfigKey(): string;

  /**
   * Gets the config key for display mode setting.
   *
   * @return string
   *   The config key.
   */
  abstract protected function getDisplayModeConfigKey(): string;

  /**
   * Gets the items to display in the block.
   *
   * @return array
   *   Array of items to display.
   */
  protected function getItems(): array {
    $config = $this->getConfiguration();
    
    // Build the query
    $query = $this->entityTypeManager->getStorage($config['entity_type'])->getQuery()
      ->condition('type', $config['bundle'])
      ->sort($config['sort_field'], $config['sort_direction'])
      ->accessCheck(TRUE);

    // Add any additional conditions defined by the child class
    $this->addQueryConditions($query);

    // Execute query
    $ids = $query->execute();
    $entities = $this->entityTypeManager
      ->getStorage($config['entity_type'])
      ->loadMultiple($ids);

    // Transform entities to render array
    return [
      'items' => array_map([$this, 'transformEntity'], $entities),
    ];
  }

  /**
   * Transform an entity into a render array for display.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to transform.
   *
   * @return array
   *   The transformed entity data.
   */
  protected function transformEntity($entity) {
    // Default implementation - override in child classes for custom formatting
    return [
      'title' => $entity->label(),
      'url' => $entity->toUrl()->toString(),
      'date' => $entity->hasField('created') ? $entity->get('created')->value : NULL,
    ];
  }

  /**
   * Gets the available entity types for this block.
   *
   * @return array
   *   Array of entity type labels keyed by entity type ID.
   */
  abstract protected function getAvailableEntityTypes(): array;

  /**
   * Gets the default entity type for this block.
   *
   * @return string
   *   The default entity type ID.
   */
  abstract protected function getDefaultEntityType(): string;

  /**
   * Gets the default bundle for this block.
   *
   * @return string
   *   The default bundle ID.
   */
  abstract protected function getDefaultBundle(): string;

  /**
   * Gets the default sort field for this block.
   *
   * @return string
   *   The default sort field.
   */
  abstract protected function getDefaultSortField(): string;

  /**
   * Gets the available bundles for an entity type.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   *
   * @return array
   *   Array of bundle labels keyed by bundle ID.
   */
  protected function getAvailableBundles(string $entity_type_id): array {
    $bundles = \Drupal::service('entity_type.bundle.info')
      ->getBundleInfo($entity_type_id);
    
    return array_map(function ($bundle) {
      return $bundle['label'];
    }, $bundles);
  }

  /**
   * Gets the available sort fields for an entity type and bundle.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $bundle
   *   The bundle ID.
   *
   * @return array
   *   Array of field labels keyed by field name.
   */
  protected function getAvailableSortFields(string $entity_type_id, string $bundle): array {
    $fields = \Drupal::service('entity_field.manager')
      ->getFieldDefinitions($entity_type_id, $bundle);
    
    $sort_fields = [];
    foreach ($fields as $field_name => $field) {
      $sort_fields[$field_name] = $field->getLabel();
    }
    
    return $sort_fields;
  }

  /**
   * Adds additional query conditions.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   The query to modify.
   */
  protected function addQueryConditions($query) {
    // Override in child classes to add specific conditions
  }

  /**
   * Handle AJAX pager requests.
   */
  public function handleAjaxPager(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    $page = \Drupal::request()->query->get('page', 0);
    
    // Rebuild block content with new page
    $build = $this->build();
    
    // Replace content
    $response->addCommand(
      new ReplaceCommand(
        '.block-' . str_replace('_', '-', $this->getPluginId()),
        $build
      )
    );
    
    return $response;
  }

  /**
   * Build pager data array.
   */
  protected function buildPagerData($total_items, $page_size, $current_page) {
    $total_pages = max(1, ceil($total_items / $page_size));
    $current_page = min(max(0, $current_page), $total_pages - 1);
    
    return [
      'total' => $total_items,
      'per_page' => $page_size,
      'current_page' => $current_page,
      'total_pages' => $total_pages,
    ];
  }

  /**
   * Loads dashboard items of a specific type.
   *
   * @param string $type
   *   The type of dashboard items to load (e.g., 'news', 'event').
   * @param int $limit
   *   The number of items to load.
   *
   * @return \Drupal\dh_dashboard\Entity\DashboardItem[]
   *   An array of dashboard items.
   */
  protected function loadDashboardItems($type, $limit = 10) {
    $storage = $this->entityTypeManager->getStorage('dashboard_item');
    $query = $storage->getQuery()
      ->condition('type', $type)
      ->sort('date', 'DESC')
      ->range(0, $limit)
      ->accessCheck(TRUE);

    $ids = $query->execute();
    return $storage->loadMultiple($ids);
  }

  /**
   * Gets the theme ID for rendering.
   *
   * @return string
   *   The theme ID.
   */
  abstract protected function getThemeId();

  /**
   * Gets the dashboard item type for this block.
   *
   * @return string
   *   The item type (e.g., 'news', 'event').
   */
  abstract protected function getItemType();
}
