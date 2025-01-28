<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for enrollment management.
 */
class EnrollmentController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new EnrollmentController.
   */
  public function __construct(Connection $database, EntityTypeManagerInterface $entity_type_manager) {
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Lists all enrollments.
   */
  public function listEnrollments() {
    // Debug log
    \Drupal::logger('dh_certificate')->debug('Starting enrollment list generation');

    $query = $this->database->select('course_enrollment', 'ce');
    $query->join('users_field_data', 'u', 'ce.uid = u.uid');
    $query->join('node_field_data', 'n', 'ce.course_id = n.nid');
    $query->fields('ce', ['id', 'uid', 'status', 'completed_date']);
    $query->fields('u', ['name']);
    $query->fields('n', ['title']);
    $query->orderBy('u.name');
    $query->orderBy('n.title');

    $results = $query->execute();

    // Group enrollments by user
    $enrollments = [];
    foreach ($results as $record) {
      // Ensure ID is properly formatted
      $record->id = (string) $record->id;
      \Drupal::logger('dh_certificate')->debug('Processing enrollment @id', ['@id' => $record->id]);

      $uid = $record->uid;
      if (!isset($enrollments[$uid])) {
        $enrollments[$uid] = [
          'user' => $record->name,
          'enrollments' => [],
          'completed' => 0,
          'in_progress' => 0,
          'pending' => 0,
        ];
      }

      $enrollments[$uid]['enrollments'][] = [
        'id' => $record->id,  // Using string ID
        'course' => $record->title,
        'status' => ucfirst($record->status),
        'completed_date' => $record->completed_date ? date('Y-m-d', $record->completed_date) : '—',
      ];

      // Update counters
      switch ($record->status) {
        case 'completed':
          $enrollments[$uid]['completed']++;
          break;
        case 'in-progress':
          $enrollments[$uid]['in_progress']++;
          break;
        default:
          $enrollments[$uid]['pending']++;
      }
    }

    // Add debug logging
    \Drupal::logger('dh_certificate')->debug('Found @count enrollments', [
      '@count' => count($enrollments)
    ]);

    return [
      '#theme' => 'dh_certificate_enrollment_list',  // Changed from admin_enrollments
      '#title' => $this->t('Course Enrollments'),
      '#enrollments' => $enrollments,
      '#cache' => [
        'max-age' => 0,
      ],
      '#attached' => [
        'library' => [
          'dh_certificate/certificate-admin',
          'dh_certificate/enrollment-delete'
        ],
      ],
    ];
  }

  /**
   * Ajax callback for enrollment listing.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response containing enrollment data.
   */
  public function ajaxEnrollmentListing(Request $request) {
    $query = $this->database->select('course_enrollment', 'ce')
      ->fields('ce');
    $query->join('users_field_data', 'u', 'ce.uid = u.uid');
    $query->join('node_field_data', 'n', 'ce.course_id = n.nid');
    $query->fields('u', ['name']);
    $query->fields('n', ['title']);
    
    // Add search condition if provided
    if ($search = $request->query->get('search')) {
      $or = $query->orConditionGroup()
        ->condition('u.name', '%' . $this->database->escapeLike($search) . '%', 'LIKE')
        ->condition('n.title', '%' . $this->database->escapeLike($search) . '%', 'LIKE');
      $query->condition($or);
    }
    
    // Add status filter if provided
    if ($status = $request->query->get('status')) {
      $query->condition('ce.status', $status);
    }
    
    $query->orderBy('u.name');
    $query->orderBy('n.title');

    $results = $query->execute();
    
    $enrollments = [];
    foreach ($results as $record) {
      $enrollments[] = [
        'id' => $record->id,
        'user' => $record->name,
        'course' => $record->title,
        'status' => $record->status,
        'completed_date' => $record->completed_date ? date('Y-m-d', $record->completed_date) : '',
      ];
    }

    return new JsonResponse([
      'enrollments' => $enrollments,
      'total' => count($enrollments),
    ]);
  }

  /**
   * Debug method to list all module routes.
   */
  public function debugRoutes() {
    /** @var \Drupal\Core\Routing\RouteProvider $route_provider */
    $route_provider = \Drupal::service('router.route_provider');
    $routes = $route_provider->getAllRoutes();
    
    $output = [];
    foreach ($routes as $route_name => $route) {
      if (strpos($route_name, 'dh_certificate') === 0) {
        $output[$route_name] = [
          'path' => $route->getPath(),
          'defaults' => $route->getDefaults(),
          'requirements' => $route->getRequirements(),
        ];
      }
    }
    
    return [
      '#markup' => '<pre>' . print_r($output, TRUE) . '</pre>',
      '#cache' => ['max-age' => 0],
    ];
  }

  /**
   * Lists all routes in the module.
   */
  public function listRoutes() {
    $route_provider = \Drupal::service('router.route_provider');
    $routes = $route_provider->getAllRoutes();
    
    $headers = ['Route Name', 'Path', 'Controller/Form', 'Permissions'];
    $rows = [];
    
    foreach ($routes as $route_name => $route) {
      if (strpos($route_name, 'dh_certificate') === 0) {
        $defaults = $route->getDefaults();
        $controller = isset($defaults['_controller']) ? $defaults['_controller'] : 
                     (isset($defaults['_form']) ? $defaults['_form'] : '');
        
        $rows[] = [
          $route_name,
          $route->getPath(),
          $controller,
          implode(', ', $route->getRequirements()),
        ];
      }
    }

    return [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows,
      '#empty' => $this->t('No routes found'),
    ];
  }

}
