<?php

/**
 * @file
 * Contains \Drupal\dh_dashboard\Controller\DashboardController.
 */

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dh_dashboard\Services\DashboardManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Layout\LayoutPluginManagerInterface;

class DashboardController extends ControllerBase {
  protected $dashboardManager;
  protected $layoutPluginManager;

  /**
   * Constructs a new DashboardController object.
   *
   * @param \Drupal\dh_dashboard\Services\DashboardManager $dashboard_manager
   *   The dashboard manager service.
   * @param \Drupal\Core\Layout\LayoutPluginManagerInterface $layout_plugin_manager
   *   The layout plugin manager service.
   */
  public function __construct(
    DashboardManager $dashboard_manager,
    LayoutPluginManagerInterface $layout_plugin_manager
  ) {
    $this->dashboardManager = $dashboard_manager;
    $this->layoutPluginManager = $layout_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_dashboard.manager'),
      $container->get('plugin.manager.core.layout')
    );
  }

  /**
   * Loads and builds the dashboard layout.
   *
   * @param string $dashboard_type
   *   The type of dashboard to load.
   *
   * @return array
   *   A render array for the dashboard.
   */
  protected function loadDashboardLayout($dashboard_type) {
    // For now, let's return a simple layout with placeholder content
    $layout = $this->layoutPluginManager->createInstance('layout_twocol');
    
    $build = $layout->build([
      'first' => [
        '#markup' => $this->t('First column content for @type dashboard', 
          ['@type' => $dashboard_type]),
      ],
      'second' => [
        '#markup' => $this->t('Second column content for @type dashboard',
          ['@type' => $dashboard_type]),
      ],
    ]);

    return $build;
  }

  public function content() {
    $account = $this->currentUser();
    $dashboard_type = $this->dashboardManager->getUserDashboard($account);
    
    return $this->loadDashboardLayout($dashboard_type);
  }
}
