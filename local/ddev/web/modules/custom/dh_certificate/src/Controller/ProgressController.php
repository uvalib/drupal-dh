<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for certificate progress management.
 */
class ProgressController extends ControllerBase {

  /**
   * Displays an overview of all certificate progress.
   *
   * @return array
   *   A render array representing the progress overview page.
   */
  public function adminOverview() {
    $build = [
      '#theme' => 'dh_certificate_progress_overview',
      '#title' => $this->t('Certificate Progress Overview'),
      '#progress_data' => [],
    ];

    // Get progress data from service
    $progress_service = \Drupal::service('dh_certificate.progress');
    $build['#progress_data'] = $progress_service->getAllProgress();

    return $build;
  }

}
