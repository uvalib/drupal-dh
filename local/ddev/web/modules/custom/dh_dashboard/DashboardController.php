<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dh_dashboard\Services\DashboardManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Layout\LayoutPluginManagerInterface;
use Drupal\Core\Block\BlockManagerInterface;

class DashboardController extends ControllerBase
{
    protected $dashboardManager;
    protected $layoutPluginManager;
    protected $blockManager;

  /**
   * Constructs a new DashboardController object.
   */
    public function __construct(
        DashboardManager $dashboard_manager,
        LayoutPluginManagerInterface $layout_plugin_manager,
        BlockManagerInterface $block_manager
    ) {
        $this->dashboardManager = $dashboard_manager;
        $this->layoutPluginManager = $layout_plugin_manager;
        $this->blockManager = $block_manager;
    }

  /**
   * {@inheritdoc}
   */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('dh_dashboard.manager'),
            $container->get('plugin.manager.core.layout'),
            $container->get('plugin.manager.block')
        );
    }

  /**
   * Loads the certificate dashboard layout.
   */
    protected function loadCertificateDashboard()
    {
        $layout = $this->layoutPluginManager->createInstance('layout_twocol');

      // Create progress block
        $progress_block = $this->blockManager
        ->createInstance('dh_certificate_progress');
    
      // Create requirements form block
        $requirements_block = $this->blockManager
        ->createInstance('dh_certificate_requirements_form');

        $build = $layout->build([
        'first' => [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('DH Certificate Progress'),
        ],
        'progress' => $progress_block->build(),
        ],
        'second' => [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Certificate Requirements'),
        ],
        'requirements' => $requirements_block->build(),
        ],
        ]);

        return $build;
    }

  /**
   * Loads and builds the dashboard layout.
   */
    protected function loadDashboardLayout($dashboard_type)
    {
        if ($dashboard_type === 'dh_certificate') {
            return $this->loadCertificateDashboard();
        }

      // Default dashboard layout
        $layout = $this->layoutPluginManager->createInstance('layout_twocol');
        return $layout->build([
        'first' => [
        '#markup' => $this->t(
            'First column content for @type dashboard',
            ['@type' => $dashboard_type]
        ),
        ],
        'second' => [
        '#markup' => $this->t(
            'Second column content for @type dashboard',
            ['@type' => $dashboard_type]
        ),
        ],
        ]);
    }

  /**
   * Displays the dashboard.
   */
    public function content()
    {
        $account = $this->currentUser();
        $dashboard_type = $this->dashboardManager->getUserDashboard($account);
    
        return $this->loadDashboardLayout($dashboard_type);
    }
}
