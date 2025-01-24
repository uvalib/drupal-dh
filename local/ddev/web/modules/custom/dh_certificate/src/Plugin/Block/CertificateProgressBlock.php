<?php

namespace Drupal\dh_certificate\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\dh_certificate\ProgressManagerInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Url;

/**
 * Provides a block showing certificate progress.
 *
 * @Block(
 *   id = "dh_certificate_progress",
 *   admin_label = @Translation("Certificate Progress"),
 *   category = @Translation("Digital Humanities")
 * )
 */
class CertificateProgressBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The certificate progress service.
   *
   * @var \Drupal\dh_certificate\ProgressManagerInterface
   */
  protected $progressManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * Constructs a new CertificateProgressBlock.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ProgressManagerInterface $progress_manager, AccountProxyInterface $current_user, RouteProviderInterface $route_provider) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->progressManager = $progress_manager;
    $this->currentUser = $current_user;
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('dh_certificate.progress_manager'),
      $container->get('current_user'),
      $container->get('router.route_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $progress = $this->progressManager->getUserProgress($this->currentUser);
    
    // Debug raw progress data
    \Drupal::logger('dh_certificate')->debug('Raw progress data: <pre>@data</pre>', [
      '@data' => print_r($progress, TRUE),
    ]);
    
    // Add course entities to progress data if not present
    if (!empty($progress['courses'])) {
      $course_storage = \Drupal::entityTypeManager()->getStorage('node');
      $progress['course_entities'] = [];
      
      foreach ($progress['courses'] as $course) {
        if (!empty($course['nid'])) {
          $course_node = $course_storage->load($course['nid']);
          if ($course_node) {
            $progress['course_entities'][] = [
              'entity' => $course_node->toArray(),  // Convert entity to array for debug view
              'status' => $course['status'] ?? 'unknown',
              'completed_date' => $course['completed_date'] ?? NULL,
              'credits' => $course['credits'] ?? 0,
            ];
          }
        }
      }
    }

    return [
      '#theme' => 'dh_certificate_progress',
      '#progress' => $progress,
      '#is_admin' => $this->currentUser->hasPermission('administer dh certificate'),
      '#debug' => $progress,  // Pass raw progress data for tree view
      '#settings_url' => Url::fromUri('internal:/admin/config/dh-certificate')->toString(),
      '#attached' => [
        'library' => [
          'dh_certificate/certificate-progress',
          'dh_certificate/debug-tree',
        ],
      ],
      '#cache' => [
        'contexts' => ['user'],
        'tags' => ['dh_certificate_progress', 'node_list'],
        'max-age' => 0,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'view dh dashboard');
  }

}
