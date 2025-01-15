<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
  public function build() {
    $config = $this->configFactory->get('dh_dashboard.settings');
    $show_debug = $config->get('show_debug') ?: FALSE;
    $items_per_page = (int) $config->get($this->getItemsPerPageConfigKey());
    $display_mode = $config->get($this->getDisplayModeConfigKey()) ?: 'grid';

    return [
      '#theme' => $this->getThemeHook(),
      '#items' => $this->getItems(),
      '#show_debug' => $show_debug,
      '#items_per_page' => $items_per_page,
      '#display_mode' => $display_mode,
      '#attributes' => new Attribute(['class' => [$this->getBlockClass()]]),
      '#attached' => [
        'library' => ['dh_dashboard/dashboard'],
        'drupalSettings' => [
          'dhDashboard' => [
            'items_per_page' => $items_per_page ?: 3,
          ],
        ],
      ],
      '#cache' => ['max-age' => 0],
    ];
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
