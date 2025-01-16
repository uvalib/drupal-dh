<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
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
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory,
    AccountInterface $current_user
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
      $container->get('current_user')
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
      'category_filter' => 'all',
      'use_compact_mode' => FALSE,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['items_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Items per page'),
      '#default_value' => $config['items_per_page'] ?? 3,
      '#min' => 1,
    ];

    return $form;
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
      '#theme' => $this->getThemeHook(),
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
   * Gets the theme hook to use for rendering.
   *
   * @return string
   *   The theme hook name.
   */
  abstract protected function getThemeHook(): string;

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
  abstract protected function getItems(): array;

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
}
