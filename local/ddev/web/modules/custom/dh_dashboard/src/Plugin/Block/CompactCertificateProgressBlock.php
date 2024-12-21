<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_dashboard\Services\CertificateService;

/**
 * Provides a compact certificate progress block.
 *
 * @Block(
 *   id = "compact_certificate_progress_block",
 *   admin_label = @Translation("Compact Certificate Progress"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class CompactCertificateProgressBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $certificateService;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, CertificateService $certificate_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->certificateService = $certificate_service;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('dh_dashboard.certificate_service')
    );
  }

  public function build() {
    $progress_data = $this->certificateService->getCurrentProgress(\Drupal::currentUser()->id());

    return [
      '#theme' => 'certificate_progress_compact',
      '#progress' => $progress_data,
      '#attached' => [
        'library' => ['dh_dashboard/certificate-progress'],
      ],
    ];
  }
}
