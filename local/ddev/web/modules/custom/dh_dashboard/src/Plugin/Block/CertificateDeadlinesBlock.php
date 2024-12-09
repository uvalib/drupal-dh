<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a block displaying upcoming certificate deadlines.
 *
 * @Block(
 *   id = "dh_certificate_deadlines",
 *   admin_label = @Translation("DH Certificate Deadlines"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class CertificateDeadlinesBlock extends BlockBase implements ContainerFactoryPluginInterface {
  
  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new CertificateDeadlinesBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Mock deadline data - replace with actual deadline tracking
    $deadlines = [
      [
        'module' => 'Final Project',
        'date' => '2024-12-15',
        'type' => 'submission',
      ],
      [
        'module' => 'Research Methods',
        'date' => '2024-11-30',
        'type' => 'quiz',
      ],
    ];

    return [
      '#theme' => 'dh_certificate_deadlines',
      '#deadlines' => $deadlines,
      '#attached' => [
        'library' => ['dh_dashboard/certificate-deadlines'],
      ],
    ];
  }
}
