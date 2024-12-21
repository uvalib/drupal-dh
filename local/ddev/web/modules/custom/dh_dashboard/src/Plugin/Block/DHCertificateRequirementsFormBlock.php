<?php
// modules/custom/dh_dashboard/src/Plugin/Block/DHCertificateRequirementsFormBlock.php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a block for managing certificate requirements.
 *
 * @Block(
 *   id = "dh_certificate_requirements_form",
 *   admin_label = @Translation("DH Certificate Requirements Form"),
 *   category = @Translation("Digital Humanities")
 * )
 */
class DHCertificateRequirementsFormBlock extends BlockBase implements ContainerFactoryPluginInterface {
  protected $currentUser;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\dh_certificate\Form\CertificateRequirementsForm');
    
    return [
      '#type' => 'container',
      'form' => $form,
      '#cache' => [
        'contexts' => ['user'],
      ],
    ];
  }
}
