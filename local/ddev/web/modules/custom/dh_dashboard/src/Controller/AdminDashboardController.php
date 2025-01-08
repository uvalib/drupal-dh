<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;

class AdminDashboardController extends ControllerBase {

  public function content() {
    return [
      '#theme' => 'dh_admin_dashboard',
      '#attached' => [
        'library' => ['dh_dashboard/dashboard'],
      ],
    ];
  }

}
