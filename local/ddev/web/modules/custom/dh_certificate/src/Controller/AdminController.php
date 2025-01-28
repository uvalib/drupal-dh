<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_certificate\ProgressManagerInterface;

/**
 * Controller for certificate administration pages.
 */
class AdminController extends ControllerBase {

  /**
   * The progress manager.
   *
   * @var \Drupal\dh_certificate\ProgressManagerInterface
   */
  protected $progressManager;

  /**
   * Constructs an AdminController object.
   *
   * @param \Drupal\dh_certificate\ProgressManagerInterface $progress_manager
   *   The progress manager service.
   */
  public function __construct(ProgressManagerInterface $progress_manager) {
    $this->progressManager = $progress_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.progress_manager')
    );
  }

  /**
   * Displays an overview of certificate administration.
   *
   * @return array
   *   A render array representing the administrative overview page.
   */
  public function overview() {
    $build = [
      '#theme' => 'dh_certificate_admin_overview',
      '#title' => $this->t('DH Certificate Administration'),
      '#stats' => $this->getOverviewStats(),
      '#attached' => [
        'library' => ['dh_certificate/certificate-admin'],
      ],
    ];

    return $build;
  }

  /**
   * Gets overview statistics.
   *
   * @return array
   *   Array of overview statistics.
   */
  protected function getOverviewStats() {
    return [
      'total_students' => $this->getTotalStudents(),
      'active_courses' => $this->getActiveCourses(),
      'progress_summary' => $this->progressManager->getReports()['summary'],
    ];
  }

  /**
   * Gets the total number of enrolled students.
   */
  protected function getTotalStudents() {
    return $this->entityTypeManager()
      ->getStorage('user')
      ->getQuery()
      ->condition('status', 1)
      ->condition('roles', 'student')
      ->accessCheck(TRUE)
      ->count()
      ->execute();
  }

  /**
   * Gets the number of active courses.
   */
  protected function getActiveCourses() {
    return $this->entityTypeManager()
      ->getStorage('node')
      ->getQuery()
      ->condition('type', 'course')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->count()
      ->execute();
  }

}
