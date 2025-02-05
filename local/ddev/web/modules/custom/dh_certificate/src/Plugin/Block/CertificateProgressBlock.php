<?php

namespace Drupal\dh_certificate\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\dh_certificate\Progress\ProgressManagerInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityTypeManagerInterface;

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
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new CertificateProgressBlock.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ProgressManagerInterface $progress_manager, AccountProxyInterface $current_user, RouteProviderInterface $route_provider, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->progressManager = $progress_manager;
    $this->currentUser = $current_user;
    $this->routeProvider = $route_provider;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('dh_certificate.progress'),
      $container->get('current_user'),
      $container->get('router.route_provider'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $progress = $this->progressManager->getUserProgress($this->currentUser);
    if (!$progress) {
      return [];
    }

    // Build safe progress data without field dependencies
    return [
      '#theme' => 'certificate_progress_block',
      '#progress' => [
        'total_courses' => $progress['total_courses'] ?? 0,
        'completed_courses' => $progress['completed_courses'] ?? 0,
        'percentage' => $progress['total_percentage'] ?? 0,
        'pending_actions' => $progress['pending_actions'] ?? 0,
        'courses' => array_map(function($course) {
          return [
            'title' => $course['title'] ?? '',
            'status' => $course['status'] ?? 'pending',
            'mnemonic' => $course['mnemonic'] ?? '',
          ];
        }, $progress['courses'] ?? []),
      ],
      '#cache' => [
        'contexts' => ['user'],
        'tags' => ['user:' . $this->currentUser->id()],
        'max-age' => 300,
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
