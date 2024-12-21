<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dh_dashboard\Services\DashboardManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Layout\LayoutPluginManagerInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;

class DashboardController extends ControllerBase
{
    protected $dashboardManager;
    protected $layoutPluginManager;
    protected $blockManager;
    protected $entityDisplayRepository;
    protected $uuidService;

    public function __construct(
        DashboardManager $dashboard_manager,
        LayoutPluginManagerInterface $layout_plugin_manager,
        BlockManagerInterface $block_manager,
        EntityDisplayRepositoryInterface $entity_display_repository,
        UuidInterface $uuid_service
    ) {
        $this->dashboardManager = $dashboard_manager;
        $this->layoutPluginManager = $layout_plugin_manager;
        $this->blockManager = $block_manager;
        $this->entityDisplayRepository = $entity_display_repository;
        $this->uuidService = $uuid_service;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('dh_dashboard.manager'),
            $container->get('plugin.manager.core.layout'),
            $container->get('plugin.manager.block'),
            $container->get('entity_display.repository'),
            $container->get('uuid')
        );
    }

    public function build() {
        \Drupal::messenger()->deleteAll();

        // Create a new section with three column layout
        $section = new Section('layout_threecol_33_34_33');

        // Add the news feed block
        $section->appendComponent(new SectionComponent(
            $this->uuidService->generate(),
            'first',
            [
                'id' => 'dh_dashboard_news',
                'label' => 'Dashboard News Feed',
                'provider' => 'dh_dashboard',
                'label_display' => 'visible',
                'context_mapping' => [],
            ]
        ));

        // Add the certificate progress block
        $section->appendComponent(new SectionComponent(
            $this->uuidService->generate(),
            'second',
            [
                'id' => 'compact_certificate_progress_block', // Changed from certificate_progress_block
                'label' => 'Certificate Progress',
                'provider' => 'dh_dashboard',
                'label_display' => 'visible',
                'context_mapping' => [],
            ]
        ));

        // Add the program info block
        $section->appendComponent(new SectionComponent(
            $this->uuidService->generate(),
            'third',
            [
                'id' => 'program_info_block',
                'label' => 'Program Info',
                'provider' => 'dh_dashboard',
                'label_display' => 'visible',
                'context_mapping' => [],
            ]
        ));

        // Build the final render array
        $build['layout'] = $section->toRenderArray();
        $build['#attached']['library'][] = 'dh_dashboard/dashboard';
        
        return $build;
    }

    /**
     * Display certificate progress page.
     *
     * @param \Drupal\Core\Session\AccountInterface $user
     *   The user account.
     *
     * @return array
     *   Render array for the certificate progress page.
     */
    public function certificateProgress(AccountInterface $user) {
        $block = $this->blockManager->createInstance('certificate_progress_block');
        return [
            '#type' => 'container',
            '#attributes' => ['class' => ['certificate-progress-page']],
            'progress' => $block->build(),
        ];
    }
}