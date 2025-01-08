<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;

class StudentDashboardController extends ControllerBase {

  public function content() {
    return [
      '#theme' => 'dh_student_dashboard',
      '#attached' => [
        'library' => ['dh_dashboard/dashboard'],
      ],
    ];
  }

}
