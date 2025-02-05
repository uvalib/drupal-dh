<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_certificate\Progress\ProgressManagerInterface;

/**
 * Controller for DH Certificate module.
 */
class DHCertificateController extends ControllerBase {

  /**
   * The progress manager.
   *
   * @var \Drupal\dh_certificate\Progress\ProgressManagerInterface
   */
  protected $progressManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.progress')
    );
  }

  /**
   * Constructs a DHCertificateController object.
   *
   * @param \Drupal\dh_certificate\ProgressManagerInterface $progress_manager
   *   The progress manager service.
   */
  public function __construct(ProgressManagerInterface $progress_manager) {
    $this->progressManager = $progress_manager;
  }

  /**
   * Displays the admin progress page.
   */
  public function adminProgress() {
    return [
      '#theme' => 'dh_certificate_admin_progress',
      '#progress' => $this->progressManager->getAllProgress(),
      '#attached' => [
        'library' => ['dh_certificate/admin'],
      ],
    ];
  }

  /**
   * Displays the admin reports page.
   */
  public function adminReports() {
    return [
      '#theme' => 'dh_certificate_admin_reports',
      '#reports' => $this->progressManager->getReports(),
    ];
  }

  /**
   * Displays the admin enrollments page.
   */
  public function adminEnrollments() {
    return [
      '#theme' => 'dh_certificate_admin_enrollments',
      '#enrollments' => $this->progressManager->getAllEnrollments(),
    ];
  }

  /**
   * Displays the course overview page.
   */
  public function courseOverview() {
    return [
      '#markup' => $this->t('Course Management'),
    ];
  }

  /**
   * Displays the progress overview page.
   */
  public function progressOverview() {
    return [
      '#theme' => 'dh_certificate_progress',
      '#progress' => $this->progressManager->getAllProgress(),
    ];
  }

  /**
   * Displays the user progress page.
   */
  public function userProgress($user) {
    return [
      '#theme' => 'dh_certificate_progress',
      '#progress' => $this->progressManager->getUserProgress($user),
    ];
  }

  /**
   * Displays the dashboard block.
   */
  public function dashboardBlock() {
    $is_admin = $this->currentUser()->hasPermission('administer dh certificate');
    $progress = $this->progressManager->getUserProgress($this->currentUser());

    return [
      '#theme' => 'dh_certificate_progress',
      '#progress' => $progress,
      '#is_admin' => $is_admin,
      '#admin_url' => $is_admin ? Url::fromRoute('dh_certificate.admin_settings')->toString() : null,
    ];
  }

  /**
   * Displays the overview page.
   */
  public function overview() {
    // Organize routes by category
    $route_categories = [
      'Configuration' => [
        [
          'path' => '/admin/config/dh-certificate/settings',
          'description' => $this->t('Configure global certificate settings'),
          'permission' => 'administer dh certificate settings',
        ],
        [
          'path' => '/admin/config/dh-certificate/requirements',
          'description' => $this->t('Manage certificate requirements'),
          'permission' => 'administer certificate requirements',
        ],
      ],
      'Course Management' => [
        [
          'path' => '/admin/config/dh-certificate/courses',
          'description' => $this->t('Manage certificate courses'),
          'permission' => 'administer dh certificate courses',
        ],
        [
          'path' => '/admin/config/dh-certificate/courses/add',
          'description' => $this->t('Add new courses'),
          'permission' => 'administer certificate courses',
        ],
      ],
      'Enrollment Management' => [
        [
          'path' => '/admin/config/dh-certificate/enrollments',
          'description' => $this->t('View and manage user enrollments'),
          'permission' => 'administer dh certificate',
        ],
        [
          'path' => '/admin/config/dh-certificate/enrollment/add',
          'description' => $this->t('Add new enrollment'),
          'permission' => 'administer dh certificate',
        ],
      ],
      'Progress Tracking' => [
        [
          'path' => '/admin/config/dh-certificate/progress',
          'description' => $this->t('View all certificate progress'),
          'permission' => 'administer dh certificate',
        ],
        [
          'path' => '/certificate/progress',
          'description' => $this->t('View personal progress'),
          'permission' => 'view own certificate progress',
        ],
      ],
    ];

    // Main admin links
    $admin_links = [
      [
        'title' => $this->t('Configure Settings'),
        'url' => Url::fromRoute('dh_certificate.settings')->toString(),
        'primary' => true,
      ],
      [
        'title' => $this->t('Manage Courses'),
        'url' => Url::fromRoute('dh_certificate.courses')->toString(),
      ],
      [
        'title' => $this->t('Configure Requirements'),
        'url' => Url::fromRoute('dh_certificate.requirements')->toString(),
      ],
      [
        'title' => $this->t('View Progress'),
        'url' => Url::fromRoute('dh_certificate.admin_progress')->toString(),
      ],
    ];

    // Permission links
    $permission_links = [
      [
        'title' => $this->t('Configure Permissions'),
        'url' => Url::fromRoute(
          'user.admin_permissions', [], [
            'fragment' => 'module-dh_certificate',
          ]
        )->toString(),
      ],
      [
        'title' => $this->t('Configure Roles'),
        'url' => Url::fromRoute('entity.user_role.collection')->toString(),
      ],
    ];

    return [
      '#theme' => 'dh_certificate_admin_overview',
      '#description' => $this->t('Configure Digital Humanities certificate settings, manage courses, and track student progress.'),
      '#admin_links' => $admin_links,
      '#route_categories' => $route_categories,
      '#permissions_description' => $this->t('Configure who can manage certificates and view progress.'),
      '#permission_links' => $permission_links,
      '#attached' => [
        'library' => ['dh_certificate/certificate-admin'],
      ],
    ];
  }

  /**
   * Displays a list of certificate courses.
   *
   * @return array
   *   A render array representing the courses list page.
   */
  public function coursesList() {
    $build = [
      '#theme' => 'certificate_course_list',
      '#categories' => [
        'Required' => [
          ['title' => 'DH 101', 'credits' => 3, 'next_offered' => '2024 Spring'],
          ['title' => 'DH 201', 'credits' => 3, 'next_offered' => '2024 Fall'],
        ],
        'Electives' => [
          ['title' => 'DH 301', 'credits' => 3, 'next_offered' => '2024 Spring'],
          ['title' => 'DH 302', 'credits' => 3, 'next_offered' => '2024 Fall'],
        ],
      ],
      '#attached' => [
        'library' => ['dh_certificate/certificate-courses'],
      ],
    ];
    return $build;
  }

  public function courseList() {
    // ...existing code...
    return [
      '#theme' => 'dh_certificate_course_listing',
      '#title' => $this->t('Certificate Courses'),
      '#categories' => $categories,
      '#view_mode' => 'student',
      // ...existing code...
    ];
  }

  public function adminCourseList() {
    $courses = $this->progressManager->getAllCourseProgress();
    $categories = [
      'Required' => array_filter(
        $courses, function ($course) {
          return !empty($course['meets_requirement']);
        }
      ),
      'Electives' => array_filter(
        $courses, function ($course) {
          return empty($course['meets_requirement']);
        }
      ),
    ];

    return [
      '#theme' => 'dh_certificate_course_listing',  // This matches the theme hook
      '#title' => $this->t('Course Management'),
      '#categories' => $categories,
      '#view_mode' => 'admin',
      '#default_view' => $this->getPreferredViewMode(),
      '#paths' => [
        'add' => Url::fromRoute('node.add', ['node_type' => 'course'])->toString(),
      ],
      '#show_debug' => true,
      '#course_data' => ['courses' => $courses],
      '#attached' => [
        'library' => ['dh_certificate/course-listing'],
        'drupalSettings' => [
          'dhCertificate' => [
            'defaultView' => $this->getPreferredViewMode(),
          ],
        ],
      ],
      '#cache' => [
        'tags' => ['node_list:course'],
      ],
    ];
  }

  protected function getPreferredViewMode() {
    return $this->config('dh_certificate.settings')
      ->get('course_list_default_view') ?? 'grid';
  }

  /**
   * Displays certificate requirements.
   *
   * @return array
   *   A render array representing the requirements page.
   */
  public function requirementsList() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Certificate requirements configuration page'),
    ];
  }

  /**
   * Redirects to the settings page.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response object.
   */
  public function adminRedirect() {
    return $this->redirect('dh_certificate.admin');
  }

  /**
   * Returns the admin settings page.
   */
  public function adminSettings() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Admin settings page for DH Certificate.'),
    ];
  }

  /**
   * Displays the requirements overview page.
   *
   * @return array
   *   A render array for the requirements overview page.
   */
  public function requirementsOverview() {
    $requirements = [
      'core_courses' => $this->progressManager->getCoreCourses(),
      'elective_credits' => $this->progressManager->getRequiredElectiveCredits(),
      'due_date' => $this->progressManager->getCompletionDeadline(),
    ];

    return [
      '#theme' => 'dh_certificate_requirements',
      '#title' => $this->t('Certificate Requirements'),
      '#requirements' => $requirements,
      '#attached' => [
        'library' => ['dh_certificate/certificate-admin'],
      ],
      '#cache' => [
        'tags' => ['config:dh_certificate.settings'],
        'contexts' => ['user.permissions'],
      ],
    ];
  }

  /**
   * Displays the admin overview page.
   */
  public function adminOverview() {
    $stats = [
      'total_students' => $this->progressManager->getTotalStudents(),
      'active_courses' => $this->progressManager->getActiveCourses(),
      'progress_summary' => [
        'completed_courses' => $this->progressManager->getCompletedCoursesCount(),
      ],
    ];

    return [
      '#theme' => 'dh_certificate_admin_overview',
      '#title' => $this->t('Digital Humanities Certificate Administration'),
      '#stats' => $stats,
      '#attached' => [
        'library' => ['dh_certificate/certificate-admin'],
      ],
      '#cache' => [
        'contexts' => ['user.permissions'],
        'tags' => ['dh_certificate:progress', 'node_list:course'],
        'max-age' => 300,
      ],
    ];
  }
}