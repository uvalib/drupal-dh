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
    return [
      '#theme' => 'dh_certificate_progress',
      '#progress' => $this->progressManager->getUserProgress($this->currentUser()),
      '#is_admin' => $is_admin,
      '#admin_url' => $is_admin ? Url::fromRoute('dh_certificate.settings')->toString() : NULL,
    ];
  }

  /**
   * Displays the overview page.
   */
  public function overview() {
    return [
      '#markup' => $this->t('DH Certificate Overview'),
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
      '#type' => 'markup',
      '#markup' => $this->t('Certificate courses list page'),
    ];
    
    return $build;
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
    return $this->redirect('dh_certificate.settings');
  }

}
