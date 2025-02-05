<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for DH Dashboard functionality.
 */
class DHDashboardController extends ControllerBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new DHDashboardController.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  // ...existing code...

  protected function getLayoutBuilderPlacements($plugin_id) {
    $placements = [];
    
    // Get all config that might contain layout builder settings
    $config_names = $this->configFactory->listAll();
    foreach ($config_names as $name) {
      if (strpos($name, 'layout_builder') !== FALSE) {
        $config = $this->configFactory->get($name);
        $data = $config->getRawData();
        
        if (is_array($data) && $this->configContainsPlugin($data, $plugin_id)) {
          $placements[] = [
            'config' => $name,
            'section' => $this->getLayoutSection($data),
          ];
        }
      }
    }
    
    return $placements;
  }

  // ...existing code...
}
