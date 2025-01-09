<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;

class StudentDashboardController extends ControllerBase {

  public function content() {
    // Add some test content
    $content = [
      '#markup' => $this->t('Welcome to your student dashboard!'),
    ];

    return [
      '#theme' => 'dh_student_dashboard',
      '#content' => $content,
      '#attached' => [
        'library' => ['dh_dashboard/dashboard'],
      ],
      '#cache' => [
        'contexts' => ['user.roles'],
        'tags' => ['dh_dashboard:student'],
      ],
    ];
  }

}
