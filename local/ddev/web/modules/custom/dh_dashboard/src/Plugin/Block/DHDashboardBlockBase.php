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
      '#default_value' => $config['items_per_page'],
      '#min' => 1,
      '#max' => 50,
    ];

    $form['display_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Display mode'),
      '#default_value' => $config['display_mode'],
      '#options' => [
        'grid' => $this->t('Grid'),
        'list' => $this->t('List'),
        'cards' => $this->t('Cards'),
      ],
    ];

    $form['show_debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show debug information'),
      '#default_value' => $config['show_debug'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['items_per_page'] = $form_state->getValue('items_per_page');
    $this->configuration['display_mode'] = $form_state->getValue('display_mode');
    $this->configuration['show_debug'] = $form_state->getValue('show_debug');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    
    return [
      '#theme' => $this->getThemeHook(),
      '#items' => $this->getItems(),
      '#show_debug' => $config['show_debug'],
      '#items_per_page' => $config['items_per_page'],
      '#display_mode' => $config['display_mode'],
      '#attributes' => new Attribute(['class' => [$this->getBlockClass()]]),
      '#attached' => [
        'library' => ['dh_dashboard/dashboard'],
        'drupalSettings' => [
          'dhDashboard' => [
            'items_per_page' => $config['items_per_page'],
          ],
        ],
      ],
      '#cache' => ['max-age' => 0],
    ];
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
}
