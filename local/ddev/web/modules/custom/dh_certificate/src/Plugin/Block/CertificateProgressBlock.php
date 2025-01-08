<?php

namespace Drupal\dh_certificate\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_certificate\CertificateManagerInterface;

/**
 * Provides a certificate progress block.
 *
 * @Block(
 *   id = "dh_certificate_progress",
 *   admin_label = @Translation("Certificate Progress"),
 *   category = @Translation("DH Certificate")
 * )
 */
class CertificateProgressBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $certificateManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, CertificateManagerInterface $certificate_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->certificateManager = $certificate_manager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('dh_certificate.manager')
    );
  }

  public function build() {
    $progress = $this->certificateManager->getCertificateProgress(\Drupal::currentUser());

    return [
      '#theme' => 'dh_certificate_progress',
      '#progress' => $progress,
      '#cache' => [
        'contexts' => ['user'],
        'tags' => ['user:' . \Drupal::currentUser()->id()],
        'max-age' => 0,
      ],
    ];
  }
}
