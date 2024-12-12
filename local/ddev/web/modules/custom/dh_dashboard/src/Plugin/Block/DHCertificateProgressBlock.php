<?php
// dh_dashboard/src/Plugin/Block/DHCertificateProgressBlock.php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_dashboard\Services\DashboardManager;

/**
 * Provides a block displaying DH Certificate progress.
 *
 * @Block(
 *   id = "dh_certificate_progress",
 *   admin_label = @Translation("DH Certificate Progress"),
 *   category = @Translation("Digital Humanities")
 * )
 */
class DHCertificateProgressBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    protected $dashboardManager;

    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        DashboardManager $dashboard_manager
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->dashboardManager = $dashboard_manager;
    }

    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('dh_dashboard.manager')
        );
    }

    public function build()
    {
        $account = \Drupal::currentUser();
        $progress = $this->dashboardManager->getDHCertificateProgress($account);

        if ($progress['field_missing']) {
            return [
            '#markup' => $this->t(
                'DH Certificate tracking is not yet configured. Please install the DH Certificate module first.'
            ),
            '#cache' => [
            'tags' => ['config:field.storage.user.field_dh_requirements'],
            ],
            ];
        }

        return [
        '#theme' => 'dh_certificate_progress',
        '#progress' => $progress,
        '#attached' => [
        'library' => ['dh_dashboard/certificate_progress'],
        ],
        ];
    }
}
