<?php

namespace Drupal\dh_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

class DashboardController extends ControllerBase
{

    public function content()
    {
        $config = $this->config('dh_dashboard.settings');
        $show_debug = $config->get('show_debug');
        $user = $this->currentUser();
        
        // Add role-specific content
        if ($user->hasRole('student')) {
            // Add student-specific content here
        }

        // Debug information
        if ($show_debug) {
            \Drupal::messenger()->addStatus('Dashboard controller accessed');
        }
    
        // Check if user has student role
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
        $nodes = $storage->loadByProperties(
            [
            'type' => 'dh_dashboard',
            'title' => 'Default Dashboard',
            ]
        );
    
        if ($nodes) {
            $node = reset($nodes);
            if ($show_debug) {
                \Drupal::messenger()->addStatus('Found dashboard node: ' . $node->id());
            }
      
            // Add admin links if user has permission
            $admin_links = [];
            if ($this->currentUser()->hasPermission('administer dh dashboard')) {
                $admin_links = [
                '#type' => 'container',
                '#attributes' => ['class' => ['dashboard-admin-links', 'dashboard-admin-links--bottom', 'card']],
                'inner' => [
                '#type' => 'container',
                '#attributes' => ['class' => ['dashboard-admin-links__inner']],
                'edit' => [
                '#type' => 'link',
                '#title' => $this->t('Edit Dashboard Layout'),
                '#url' => Url::fromRoute(
                    'layout_builder.overrides.node.view', [
                    'node' => $node->id(),
                    ]
                ),
                  '#attributes' => ['class' => ['button', 'button--primary']],
                ],
                'settings' => [
                '#type' => 'link',
                '#title' => $this->t('Configure Dashboard'),
                '#url' => Url::fromRoute('dh_dashboard.admin'),
                '#attributes' => ['class' => ['button', 'button--primary']],
                ],
                'permissions' => [
                '#type' => 'link',
                '#title' => $this->t('Manage Permissions'),
                '#url' => Url::fromRoute('user.admin_permissions', [], ['fragment' => 'module-dh_dashboard']),
                '#attributes' => ['class' => ['button', 'button--primary']],
                ],
                ],
                ];
            }
      
            // Build the node render array with explicit view mode
            $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
            $build = $view_builder->view($node, 'full');
      
            return [
            '#theme' => 'dh_dashboard_page',
            '#content' => $build,
            '#admin_links' => $admin_links,
            '#debug' => $show_debug ? $debug_info : [],
            '#attached' => [
            'library' => ['dh_dashboard/dashboard'],
            ],
            '#cache' => [
            'tags' => $node->getCacheTags(),
            'contexts' => ['user.permissions', 'route.name', 'url.path'],
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
