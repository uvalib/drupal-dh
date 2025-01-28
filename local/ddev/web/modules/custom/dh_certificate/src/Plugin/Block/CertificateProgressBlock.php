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
    $database = \Drupal::database();
    
    // First, get detailed table information
    $debug_data = [
      'table_info' => [
        'exists' => $database->schema()->tableExists('course_enrollment'),
        'connection' => get_class($database),
        'prefix' => $database->getPrefix(),
      ],
      'current_user' => $this->currentUser->id(),
      'raw_counts' => [],
      'all_enrollments' => [],
    ];

    // Get all enrollments without filtering
    try {
      // Get table schema info using direct query
      $schema_query = "SHOW COLUMNS FROM {course_enrollment}";
      $debug_data['table_info']['columns'] = $database->query($schema_query)->fetchAll();
      
      $raw_query = "SELECT ce.*, n.title FROM {course_enrollment} ce JOIN {node_field_data} n ON ce.course_id = n.nid";
      $debug_data['raw_query'] = $raw_query;
      $all_results = $database->query($raw_query)->fetchAll();
      
      foreach ($all_results as $row) {
        $debug_data['all_enrollments'][] = [
          'id' => $row->id,
          'uid' => $row->uid,
          'course_id' => $row->course_id,
          'course_title' => $row->title,
          'status' => $row->status,
          'completed_date' => $row->completed_date ? date('Y-m-d H:i:s', $row->completed_date) : NULL,
        ];
      }
    }
    catch (\Exception $e) {
      $debug_data['all_enrollments_error'] = $e->getMessage();
      $debug_data['all_enrollments_trace'] = $e->getTraceAsString();
    }

    // Add raw counts
    try {
      $debug_data['raw_counts'] = [
        'total_enrollments' => $database->query("SELECT COUNT(*) FROM {course_enrollment}")->fetchField(),
        'user_enrollments' => $database->query("SELECT COUNT(*) FROM {course_enrollment} WHERE uid = :uid", [':uid' => $this->currentUser->id()])->fetchField(),
        'total_courses' => $database->query("SELECT COUNT(*) FROM {node} WHERE type = 'course'")->fetchField(),
      ];
    }
    catch (\Exception $e) {
      $debug_data['raw_counts_error'] = $e->getMessage();
    }

    // Get raw count of all enrollments
    try {
      $count_query = $database->select('course_enrollment', 'ce')
        ->countQuery();
      $debug_data['raw_counts']['total_enrollments'] = $count_query->execute()->fetchField();
      
      // Get count for current user
      $user_count = $database->select('course_enrollment', 'ce')
        ->condition('uid', $this->currentUser->id())
        ->countQuery()
        ->execute()
        ->fetchField();
      $debug_data['raw_counts']['user_enrollments'] = $user_count;
    }
    catch (\Exception $e) {
      $debug_data['raw_counts']['error'] = $e->getMessage();
    }

    // Build the main query
    $query = $database->select('course_enrollment', 'ce');
    $query->join('node_field_data', 'n', 'ce.course_id = n.nid');
    $query->leftJoin('node__field_credits', 'fc', 'n.nid = fc.entity_id');
    $query->fields('ce', ['id', 'status', 'completed_date'])
          ->fields('n', ['title', 'nid'])
          ->fields('fc', ['field_credits_value'])
          ->condition('ce.uid', $this->currentUser->id())
          ->orderBy('n.title');
    
    $debug_data['query_string'] = $query->__toString();

    try {
      $results = $query->execute()->fetchAll();
      $debug_data['query_results'] = [];
      foreach ($results as $row) {
        $debug_data['query_results'][] = [
          'id' => $row->id,
          'course_id' => $row->nid,
          'title' => $row->title,
          'status' => $row->status,
          'credits' => $row->field_credits_value,
          'completed_date' => $row->completed_date ? date('Y-m-d H:i:s', $row->completed_date) : NULL,
        ];
      }
    }
    catch (\Exception $e) {
      $debug_data['query_error'] = $e->getMessage();
    }

    // Calculate progress metrics
    $total_courses = count($results);
    $completed_courses = 0;
    $total_credits = 0;
    $completed_credits = 0;
    $courses = [];

    foreach ($results as $row) {
      $credits = (int)$row->field_credits_value ?? 0;
      $courses[] = [
        'title' => $row->title,
        'status' => $row->status,
        'credits' => $credits,
        'completed_date' => $row->completed_date,
      ];
      
      $total_credits += $credits;
      // Update status check to match enrollment command values
      if ($row->status === 'completed' || $row->status === 'complete') {  // Accept both versions
        $completed_courses++;
        $completed_credits += $credits;
      }

      // Add debug info for this specific row
      $debug_data['status_checks'][] = [
        'course' => $row->title,
        'status' => $row->status,
        'completed' => ($row->status === 'completed' || $row->status === 'complete'),
      ];
    }

    $percentage = $total_courses > 0 ? round(($completed_courses / $total_courses) * 100) : 0;

    // Prepare progress data for display
    $processed_progress = [
      'total_courses' => $total_courses,
      'completed_courses' => $completed_courses,
      'total_credits' => $total_credits,
      'completed_credits' => $completed_credits,
      'percentage' => $percentage,
      'courses' => $courses,
      'pending_actions' => $total_courses - $completed_courses,
    ];

    // Add debug data to processed progress
    $processed_progress['_debug'] = $debug_data;

    // Build the render array
    $build = [
      '#theme' => 'certificate_progress_block',
      '#progress' => $processed_progress,
      '#user' => $this->currentUser,
      '#is_admin' => $this->currentUser->hasPermission('administer dh certificate'),
      '#attributes' => [
        'class' => ['certificate-progress-block'],
      ],
      '#attached' => [
        'library' => ['dh_certificate/certificate-progress'],
      ],
      '#cache' => [
        'contexts' => ['user'],
        'tags' => ['dh_certificate_progress', 'node_list'],
        'max-age' => 0,
      ],
    ];

    if ($build['#is_admin']) {
      $build['#attributes']['class'][] = 'user-is-admin';
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'view dh dashboard');
  }

}
