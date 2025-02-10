<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\dh_certificate\Progress\ProgressManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for certificate progress management.
 */
class ProgressController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The progress manager service.
   *
   * @var \Drupal\dh_certificate\Progress\ProgressManagerInterface
   */
  protected $progressManager;

  /**
   * Constructs a new ProgressController.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\dh_certificate\Progress\ProgressManagerInterface $progress_manager
   *   The progress manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ProgressManagerInterface $progress_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->progressManager = $progress_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('dh_certificate.progress')
    );
  }

  /**
   * Displays the progress overview page.
   */
  public function overview() {
    return [
      '#theme' => 'dh_certificate_progress_overview',
      '#title' => $this->t('Progress Overview'),
      '#data' => $this->getProgressData(),
    ];
  }

  /**
   * Displays the admin overview page.
   */
  public function adminOverview() {
    return [
      '#theme' => 'dh_certificate_admin_progress',
      '#title' => $this->t('Administrative Progress Overview'),
      '#data' => $this->getProgressData(TRUE),
    ];
  }

  /**
   * Displays user progress page.
   */
  public function userProgress() {
    $account = $this->currentUser();
    return [
      '#theme' => 'dh_certificate_user_progress',
      '#title' => $this->t('My Certificate Progress'),
      '#progress' => $this->getUserProgressData($account),
    ];
  }

  /**
   * Displays admin enrollments page.
   */
  public function adminEnrollments() {
    return [
      '#theme' => 'dh_certificate_admin_enrollments',
      '#title' => $this->t('User Enrollments'),
      '#enrollments' => $this->getEnrollmentData(),
    ];
  }

  /**
   * Displays the admin progress page.
   */
  public function adminProgress() {
    // Add debugging
    \Drupal::logger('dh_certificate')->notice('adminProgress() called');
    
    $data = $this->getProgressData(TRUE);
    \Drupal::logger('dh_certificate')->notice('Progress data: @data', [
      '@data' => var_export($data, TRUE)
    ]);

    $build = [
      '#theme' => 'dh_certificate_admin_progress',
      '#title' => $this->t('Certificate Progress'),
      '#data' => $data,
      '#attached' => [
        'library' => ['dh_certificate/progress-admin'],
      ],
      '#cache' => [
        'max-age' => 0, // Disable caching for now
      ],
    ];
    
    return $build;
  }

  /**
   * Gets progress data.
   *
   * @param bool $admin
   *   Whether to get admin level data.
   */
  protected function getProgressData($admin = FALSE) {
    $data = [];
    try {
      // Get only active users with a limit
      $storage = $this->entityTypeManager->getStorage('user');
      $query = $storage->getQuery()
        ->accessCheck(TRUE)
        ->condition('status', 1)
        ->range(0, 50) // Limit to 50 users at a time
        ->sort('access', 'DESC'); // Most recently active first
      
      $uids = $query->execute();
      
      foreach ($uids as $uid) {
        $account = $storage->load($uid);
        if (!$account) {
          continue;
        }

        // Get only essential progress data
        $progress = $this->progressManager->getUserProgress($account);
        if (!$progress) {
          continue;
        }

        // Minimize data structure
        $data[] = [
          'user' => [
            'uid' => $uid,
            'name' => $account->getDisplayName(),
            'email' => $account->getEmail(),
          ],
          'progress' => [
            'overall' => (int)($progress['overall_percentage'] ?? 0),
            'courses' => [
              'completed' => (int)($progress['completed_courses'] ?? 0),
              'total' => (int)($progress['total_courses'] ?? 0),
            ],
            'last_updated' => $progress['last_updated'] ?? NULL,
          ],
        ];

        // Clear entity cache after each user to free memory
        $storage->resetCache([$uid]);
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error('Error getting progress data: @error', [
        '@error' => $e->getMessage(),
      ]);
    }
    
    return $data;
  }

  /**
   * Gets progress data for a specific user.
   */
  protected function getUserProgressData($account) {
    // Implementation details...
    return [];
  }

  /**
   * Gets enrollment data.
   */
  protected function getEnrollmentData() {
    // Implementation details...
    return [];
  }

}
