<?php
// dh_dashboard/src/Plugin/Block/DHCertificateProgressBlock.php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_dashboard\Services\CertificateService;

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
     * The certificate service.
     *
     * @var \Drupal\dh_dashboard\Services\CertificateService
     */
    protected $certificateService;

    /**
     * Constructs a new DHCertificateProgressBlock instance.
     *
     * @param array $configuration
     *   The plugin configuration.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\dh_dashboard\Services\CertificateService $certificate_service
     *   The certificate service.
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        CertificateService $certificate_service
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->certificateService = $certificate_service;
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
            $container->get('dh_dashboard.certificate_service')
        );
    }

    public function build()
    {
        $progress_data = $this->certificateService->getCurrentProgress(\Drupal::currentUser()->id());

        return [
            '#theme' => 'dh_certificate_progress',
            '#progress' => $progress_data,
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
