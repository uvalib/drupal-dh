<?php

namespace Drupal\dh_dashboard\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Service for cleaning up dashboard entities and configurations.
 */
class DashboardCleanupService
{

    protected $entityTypeManager;
    protected $configFactory;
    protected $logger;

    public function __construct(
        EntityTypeManagerInterface $entity_type_manager,
        ConfigFactoryInterface $config_factory,
        LoggerChannelFactoryInterface $logger_factory
    ) {
        $this->entityTypeManager = $entity_type_manager;
        $this->configFactory = $config_factory;
        $this->logger = $logger_factory->get('dh_dashboard');
    }

    /**
     * Clean up all dashboard module entities and configurations.
     *
     * @return array
     *   Statistics about what was cleaned up.
     */
    public function cleanup()
    {
        $stats = [
        'entities' => [],
        'configs' => 0,
        ];

        $entity_types_to_clean = [
        'block_content' => [
        'types' => [
          'dh_program_info',
          'dh_news_feed',
          'dh_dashboard_news_feed',
          'dh_dashboard_certificate_progress',
          'dh_certificate_progress_compact',
          'certificate_progress',
        ],
        ],
        'entity_view_display' => [
        'ids' => [
          'block_content.dh_dashboard_news_feed.default',
          'node.dashboard.default',
        ],
        ],
        ];

        // Clean up entities
        foreach ($entity_types_to_clean as $entity_type => $info) {
            try {
                $storage = $this->entityTypeManager->getStorage($entity_type);
        
                if (!empty($info['types'])) {
                    $ids = $storage->getQuery()
                        ->condition('type', $info['types'], 'IN')
                        ->accessCheck(false)
                        ->execute();
                } elseif (!empty($info['ids'])) {
                    $ids = $info['ids'];
                }

                if (!empty($ids)) {
                    $entities = $storage->loadMultiple($ids);
                    $storage->delete($entities);
                    $stats['entities'][$entity_type] = count($entities);
                }
            }
            catch (\Exception $e) {
                $this->logger->error(
                    'Failed to clean up @type: @message', [
                    '@type' => $entity_type,
                    '@message' => $e->getMessage(),
                    ]
                );
            }
        }

        // Clean up configuration
        $configs_to_delete = [
        'dh_dashboard.settings',
        'block.block.dh_program_info',
        'block.block.dh_news_feed',
        'block.block.dh_dashboard_news_feed',
        'block.block.dh_dashboard_certificate_progress',
        'layout_builder.layout.dh_certificate',
        'block_content.type.dh_dashboard_news_feed',
        'block_content.type.dh_certificate_progress',
        ];

        foreach ($configs_to_delete as $config_name) {
            $this->configFactory->getEditable($config_name)->delete();
        }
        $stats['configs'] = count($configs_to_delete);

        return $stats;
    }
}
