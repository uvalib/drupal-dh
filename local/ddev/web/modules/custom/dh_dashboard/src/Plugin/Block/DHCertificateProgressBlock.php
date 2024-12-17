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
    /**
     * The dashboard manager service.
     *
     * @var \Drupal\dh_dashboard\Services\DashboardManager
     */
    protected $dashboardManager;

    /**
     * Constructs a new DHCertificateProgressBlock instance.
     *
     * @param array $configuration
     *   The plugin configuration.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\dh_dashboard\Services\DashboardManager $dashboard_manager
     *   The dashboard manager service.
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        DashboardManager $dashboard_manager
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->dashboardManager = $dashboard_manager;
    }

    /**
     * {@inheritdoc}
     */
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
        return [
            '#theme' => 'dh_certificate_progress',
            '#progress' => $this->dashboardManager->getMockProgress(),
            '#attached' => [
                'library' => [
                    'dh_dashboard/certificate-progress',
                ],
            ],
            '#cache' => [
                'contexts' => ['user'],
            ],
        ];
    }
}
