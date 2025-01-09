<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

class DashboardController extends ControllerBase {

  public function content() {
    $config = $this->config('dh_dashboard.settings');
    $show_debug = $config->get('show_debug');

    // Debug information
    if ($show_debug) {
      \Drupal::messenger()->addStatus('Dashboard controller accessed');
    }
    
    // Check if user has student role
    $user = $this->currentUser();
    $roles = $user->getRoles();
    
    $debug_info = [
      '#type' => 'container',
      '#attributes' => ['class' => ['dashboard-debug']],
      'roles' => [
        '#markup' => '<h3>Debug Info:</h3><p>User roles: ' . implode(', ', $roles) . '</p>',
      ],
    ];

    // Load and check the dashboard node
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nodes = $storage->loadByProperties([
      'type' => 'dh_dashboard',
      'title' => 'Default Dashboard',
    ]);
    
    if ($nodes) {
      $node = reset($nodes);
      if ($show_debug) {
        \Drupal::messenger()->addStatus('Found dashboard node: ' . $node->id());
      }
      
      // Build the node render array with explicit view mode
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
      $build = $view_builder->view($node, 'default');
      if ($show_debug) {
        \Drupal::messenger()->addStatus('View mode: default');
      }
      
      // Debug the build array
      if ($show_debug) {
        \Drupal::messenger()->addStatus('Build array keys: ' . implode(', ', array_keys($build)));
      }
      
      // Add layout edit link - only for users with appropriate permissions
      $admin_links = [];
      if ($this->currentUser()->hasPermission('administer site configuration')) {
        $admin_links = [
          '#type' => 'container',
          '#attributes' => ['class' => ['dashboard-admin-links']],
          'links' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['admin-buttons', 'admin-buttons--spaced']],
            'layout' => [
              '#type' => 'link',
              '#title' => $this->t('Edit Layout'),
              '#url' => Url::fromUserInput('/node/' . $node->id() . '/layout'),
              '#attributes' => ['class' => ['button', 'button--secondary', 'button--small']],
            ],
            'spacer' => [
              '#markup' => '<span class="admin-buttons-spacer"></span>',
            ],
            'settings' => [
              '#type' => 'link',
              '#title' => $this->t('Settings'),
              '#url' => Url::fromRoute('dh_dashboard.admin'),
              '#attributes' => ['class' => ['button', 'button--secondary', 'button--small']],
            ],
          ],
        ];
      }

      return [
        '#type' => 'container',
        '#attributes' => ['class' => ['dh-dashboard-wrapper']],
        '#attached' => [
          'library' => ['dh_dashboard/dashboard'],
        ],
        'content' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['dh-dashboard-grid']],
          'build' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['dashboard-card']],
            'header' => [
              '#type' => 'container',
              '#attributes' => ['class' => ['dashboard-card__header']],
              'title' => ['#markup' => '<h2>Dashboard Content</h2>'],
            ],
            'content' => [
              '#type' => 'container',
              '#attributes' => ['class' => ['dashboard-card__content']],
              'build' => $build,
            ],
          ],
        ],
        'debug' => $show_debug ? [
          '#type' => 'container',
          '#attributes' => ['class' => ['dashboard-card']],
          'header' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['dashboard-card__header']],
            'title' => ['#markup' => '<h2>Debug Information</h2>'],
          ],
          'content' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['dashboard-card__content']],
            'debug' => $debug_info,
          ],
        ] : [],
        'admin_links' => $admin_links,
        '#cache' => [
          'tags' => $node->getCacheTags(),
          'contexts' => ['user.permissions'],
        ],
      ];
    }

    // Return debug content if no node found
    return [
      '#theme' => 'dh_dashboard_page',
      '#content' => [
        '#markup' => $this->t('No dashboard node found'),
      ],
      '#debug' => $show_debug ? $debug_info : [],
      '#attached' => [
        'library' => ['dh_dashboard/dashboard'],
      ],
    ];
  }

}
