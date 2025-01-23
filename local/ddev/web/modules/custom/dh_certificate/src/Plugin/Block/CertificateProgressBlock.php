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
              'entity' => $course_node,
              'status' => $course['status'] ?? 'unknown',
              'completed_date' => $course['completed_date'] ?? NULL,
              'credits' => $course['credits'] ?? 0,
            ];
          }
        }
      }
      
      \Drupal::logger('dh_certificate')->debug('Course entities loaded: @count', [
        '@count' => count($progress['course_entities']),
      ]);
    }

    // Simplified debug output
    $debug_output = [
      '#type' => 'details',
      '#title' => $this->t('Debug Info'),
      '#open' => TRUE,
      'simple' => [
        '#markup' => '<div class="debug-section"><pre>' . $this->formatDebugData($progress) . '</pre></div>',
      ],
    ];

    return [
      '#theme' => 'dh_certificate_progress',
      '#progress' => $progress,
      '#is_admin' => $this->currentUser->hasPermission('administer dh certificate'),
      '#debug' => $debug_output,
      '#attached' => [
        'library' => [
          'dh_certificate/certificate-progress',
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
   * Format debug data in a memory-efficient way.
   */
  protected function formatDebugData($data) {
    $output = [];
    
    foreach ($data as $key => $value) {
      if ($key === 'course_entities' && is_array($value)) {
        // Special handling for course entities
        $courseCount = count($value);
        $courseDetails = [];
        foreach ($value as $i => $course) {
          $entity = $course['entity'];
          $courseDetails[] = sprintf(
            '<details><summary>Course %d of %d: %s</summary><div class="course-debug">%s</div></details>',
            $i + 1,
            $courseCount,
            $entity->getTitle(),
            $this->formatCourseEntityData($course)
          );
        }
        $output[] = sprintf(
          '<strong>%s:</strong> <div class="courses-debug">%s</div>',
          $key,
          implode("\n", $courseDetails)
        );
      }
      else if (is_object($value)) {
        $value = get_class($value) . ' object';
        $output[] = sprintf('<strong>%s:</strong> %s', $key, htmlspecialchars($value));
      }
      else if (is_array($value)) {
        $output[] = sprintf(
          '<strong>%s:</strong> <pre>%s</pre>',
          $key,
          htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT))
        );
      }
      else if (is_bool($value)) {
        $value = $value ? 'true' : 'false';
        $output[] = sprintf('<strong>%s:</strong> %s', $key, $value);
      }
      else {
        $output[] = sprintf('<strong>%s:</strong> %s', $key, htmlspecialchars((string)$value));
      }
    }
    
    return implode('<br>', $output);
  }

  /**
   * Format course entity data for debug output.
   */
  protected function formatCourseEntityData($course) {
    $entity = $course['entity'];
    $output = [];
    $output[] = sprintf('<strong>Title:</strong> %s', $entity->getTitle());
    $output[] = sprintf('<strong>NID:</strong> %s', $entity->id());
    $output[] = sprintf('<strong>Status:</strong> %s', $course['status']);
    $output[] = sprintf('<strong>Credits:</strong> %s', $course['credits']);
    if ($course['completed_date']) {
      $output[] = sprintf('<strong>Completed:</strong> %s', $course['completed_date']);
    }
    return implode('<br>', $output);
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'view dh dashboard');
  }

}
