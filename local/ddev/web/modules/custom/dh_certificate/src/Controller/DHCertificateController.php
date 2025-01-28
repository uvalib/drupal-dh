<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_certificate\ProgressManagerInterface;
use Drupal\Core\Url;

/**
 * Controller for DH Certificate module.
 */
class DHCertificateController extends ControllerBase {

  /**
   * The progress manager.
   *
   * @var \Drupal\dh_certificate\ProgressManagerInterface
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
   */
  public function __construct(ProgressManagerInterface $progress_manager) {
    $this->progressManager = $progress_manager;
  }

  /**
   * Displays the admin overview page.
   */
  public function adminOverview() {
    return [
      '#markup' => $this->t('DH Certificate Administration'),
    ];
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
      '#admin_url' => $is_admin ? Url::fromRoute('dh_certificate.admin_settings')->toString() : NULL,
    ];
  }

  /**
   * Displays the overview page.
   */
  public function overview() {
    $build = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['dh-certificate-overview'],
      ],
      '#attached' => [
        'library' => ['dh_certificate/certificate-admin'],
      ],
    ];

    $build['description'] = [
      '#markup' => $this->t('Configure Digital Humanities certificate settings, manage courses, and track student progress.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $build['admin_links'] = [
      '#type' => 'details',
      '#title' => $this->t('Administrative Pages'),
      '#open' => TRUE,
      'links' => [
        '#theme' => 'links',
        '#links' => [
          'settings' => [
            'title' => $this->t('Configure Settings'),
            'url' => Url::fromRoute('dh_certificate.settings'),
            'attributes' => ['class' => ['button']],
          ],
          'courses' => [
            'title' => $this->t('Manage Courses'),
            'url' => Url::fromRoute('dh_certificate.courses'),
            'attributes' => ['class' => ['button']],
          ],
          'requirements' => [
            'title' => $this->t('Configure Requirements'),
            'url' => Url::fromRoute('dh_certificate.requirements'),
            'attributes' => ['class' => ['button']],
          ],
          'progress' => [
            'title' => $this->t('View Progress'),
            'url' => Url::fromRoute('dh_certificate.admin_progress'),
            'attributes' => ['class' => ['button']],
          ],
        ],
      ],
    ];

    $build['permissions'] = [
      '#type' => 'details',
      '#title' => $this->t('Permissions'),
      '#open' => TRUE,
      'description' => [
        '#markup' => $this->t('Configure who can manage certificates and view progress.'),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ],
      'links' => [
        '#theme' => 'links',
        '#links' => [
          'permissions' => [
            'title' => $this->t('Configure Permissions'),
            'url' => Url::fromRoute('user.admin_permissions', [], [
              'fragment' => 'module-dh_certificate',
            ]),
            'attributes' => ['class' => ['button']],
          ],
          'roles' => [
            'title' => $this->t('Configure Roles'),
            'url' => Url::fromRoute('entity.user_role.collection'),
            'attributes' => ['class' => ['button']],
          ],
        ],
      ],
    ];

    return $build;
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
      'Required' => array_filter($courses, function($course) {
        return !empty($course['meets_requirement']);
      }),
      'Electives' => array_filter($courses, function($course) {
        return empty($course['meets_requirement']);
      }),
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
      '#show_debug' => TRUE,
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

}
