<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dh_dashboard\Services\DashboardManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Layout\LayoutPluginManagerInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Session\AccountInterface;

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

    public function content() {
        \Drupal::messenger()->deleteAll();
        \Drupal::messenger()->addStatus('Dashboard loaded at: ' . date('H:i:s'));
        
        $layout = $this->layoutPluginManager->createInstance('layout_threecol_25_50_25');
        $progress_block = $this->blockManager->createInstance('certificate_progress_block');
        
        $build = $layout->build([
            'first' => [
                '#theme' => 'dh_news_feed',
                '#news' => [
                    [
                        'date' => '2024-01-15',
                        'title' => 'DH Certificate Updates',
                        'excerpt' => 'Important changes to certificate requirements coming in Fall 2024...',
                        'link' => '/news/certificate-updates',
                    ],
                    [
                        'date' => '2024-01-10',
                        'title' => 'Student Project Showcase',
                        'excerpt' => 'Join us for presentations of digital humanities projects...',
                        'link' => '/news/project-showcase',
                    ],
                ],
            ],
            'second' => [
                'progress' => $progress_block->build(),
            ],
            'third' => [
                '#theme' => 'dh_program_info',
                '#announcements' => [
                    [
                        'type' => 'deadline',
                        'title' => 'Spring 2024 Deadlines',
                        'content' => 'Certificate completion forms due April 1st',
                        'priority' => 'high',
                    ],
                    [
                        'type' => 'event',
                        'title' => 'Certificate Information Session',
                        'content' => 'Learn about the DH Certificate Program',
                        'location' => 'Wilson Hall 142',
                        'date' => 'February 1st, 3PM',
                    ],
                ],
            ],
        ]);

        $build['#prefix'] = '<div class="dh-dashboard-wrapper">';
        $build['#suffix'] = '</div>';
        $build['#attached']['library'][] = 'dh_dashboard/dashboard';

        return $build;
    }

    // Remove or comment out loadCertificateDashboard() and loadDefaultDashboard() 
    // as they're no longer needed - we're using content() directly

    public function certificateProgress(AccountInterface $user) {
        $block = $this->blockManager->createInstance('certificate_progress_block');
        return [
            '#type' => 'container',
            '#attributes' => ['class' => ['certificate-progress-page']],
            'progress' => $block->build(),
        ];
    }
}
