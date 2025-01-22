<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dh_certificate\Service\DHCertificateProgressService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for certificate-related pages.
 */
class DHCertificateController extends ControllerBase {

  /**
   * The certificate progress service.
   *
   * @var \Drupal\dh_certificate\Service\DHCertificateProgressService
   */
  protected $progressService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.progress') // Changed from progress_manager
    );
  }

  /**
   * Constructs a new DHCertificateController.
   */
  public function __construct(DHCertificateProgressService $progress_service) {
    $this->progressService = $progress_service;
  }

  public function dashboardBlock() {
    $progress = $this->progressService->getUserProgress($this->currentUser());
    
    // Debug logging
    \Drupal::logger('dh_certificate')->debug('Dashboard block progress data: @data', [
      '@data' => print_r($progress, TRUE),
    ]);

    $build = [
      '#theme' => 'certificate_progress_block',
      '#progress' => $progress,
      '#cache' => [
        'contexts' => ['user'],
        'tags' => ['certificate_progress:' . $this->currentUser()->id()],
      ],
      '#attached' => [
        'library' => ['dh_certificate/certificate-progress'],
      ],
    ];
    
    // Add debug information if enabled
    if (\Drupal::config('dh_certificate.settings')->get('show_debug')) {
      $build['debug'] = [
        '#type' => 'details',
        '#title' => $this->t('Debug Information'),
        '#open' => TRUE,
        'content' => [
          '#markup' => '<pre>' . print_r($build['#progress'], TRUE) . '</pre>',
        ],
      ];
    }
    
    return $build;
  }

  /**
   * Displays an overview of the certificate system.
   *
   * @return array
   *   A render array representing the administrative overview page.
   */
  public function overview() {
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['dh-certificate-overview']],
    ];

    $build['description'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Administrative overview of the DH Certificate system.'),
    ];

    return $build;
  }

  /**
   * Displays the certificate progress admin page.
   *
   * @return array
   *   A render array for the admin page.
   */
  public function adminProgress() {
    return [
      '#theme' => 'dh_certificate_progress_admin',
      '#attached' => [
        'library' => ['dh_certificate/admin'],
      ],
    ];
  }

}
