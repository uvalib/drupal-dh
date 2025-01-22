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

        // Load the default dashboard node
        $storage = \Drupal::entityTypeManager()->getStorage('node');
        $default_nid = $config->get('default_dashboard');
        
        // First try to load by config
        if ($default_nid) {
            $node = $storage->load($default_nid);
        }
        
        // Fallback to loading by title if no node found
        if (empty($node)) {
            $nodes = $storage->loadByProperties([
                'type' => 'dh_dashboard',
                'title' => 'Dashboard',
            ]);
            $node = reset($nodes);
        }

        if ($node) {
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
                '#url' => Url::fromRoute('dh_dashboard.settings'),
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
            'library' => ['dh_dashboard/dashboard', 'dh_dashboard/event_preview'],
            ],
            '#cache' => [
            'tags' => $node->getCacheTags(),
            'contexts' => ['user.permissions', 'route.name', 'url.path'],
            ],
            ];
        }

        // Return error message if no dashboard found
        \Drupal::messenger()->addError($this->t('No dashboard has been configured.'));
        return [
            '#theme' => 'dh_dashboard_page',
            '#content' => [
                '#markup' => $this->t('Please create a dashboard node or contact your administrator.'),
            ],
            '#debug' => $show_debug ? $debug_info : [],
            '#attached' => [
                'library' => ['dh_dashboard/dashboard'],
            ],
        ];
    }

}
