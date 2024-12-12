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

    public function __construct(
        DashboardManager $dashboard_manager,
        LayoutPluginManagerInterface $layout_plugin_manager,
        BlockManagerInterface $block_manager
    ) {
        $this->dashboardManager = $dashboard_manager;
        $this->layoutPluginManager = $layout_plugin_manager;
        $this->blockManager = $block_manager;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('dh_dashboard.manager'),
            $container->get('plugin.manager.core.layout'),
            $container->get('plugin.manager.block')
        );
    }

    protected function loadCertificateDashboard()
    {
      // Use a three column layout for certificate dashboard
        $layout = $this->layoutPluginManager->createInstance('layout_threecol_25_50_25');
    
      // Create block instances
        $progress_block = $this->blockManager
        ->createInstance('dh_certificate_progress')
        ->build();
    
        $deadlines_block = $this->blockManager
        ->createInstance('dh_certificate_deadlines')
        ->build();
    
      // Create a view block for recent certificate content
        $recent_content = views_embed_view('certificate_content', 'block_1');

        $build = $layout->build([
        'first' => [
        'progress' => $progress_block,
        'deadlines' => $deadlines_block,
        ],
        'second' => [
        'content' => $recent_content,
        ],
        'third' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['quick-links']],
        'links' => [
          '#theme' => 'item_list',
          '#items' => [
            ['#markup' => $this->t('Certificate Requirements')],
            ['#markup' => $this->t('Submit Assignment')],
            ['#markup' => $this->t('Contact Advisor')],
          ],
        ],
        ],
        ]);

        return $build;
    }

    protected function loadDefaultDashboard()
    {
      // Implement default dashboard layout
        $layout = $this->layoutPluginManager->createInstance('layout_twocol');
    
        return $layout->build([
        'first' => [
        '#markup' => $this->t('<h2>Welcome to your dashboard</h2>'),
        ],
        'second' => [
        '#markup' => $this->t('Quick links and notifications will appear here'),
        ],
        ]);
    }

    public function content()
    {
        $account = $this->currentUser();
        $dashboard_type = $this->dashboardManager->getUserDashboard($account);
    
        if ($dashboard_type === 'dh_certificate') {
            return $this->loadCertificateDashboard();
        }
    
        return $this->loadCertificateDashboard();
      // return $this->loadDefaultDashboard();
    }
}
