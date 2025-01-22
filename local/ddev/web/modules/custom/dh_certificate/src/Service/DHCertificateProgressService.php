<?php

namespace Drupal\dh_certificate\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;
use Drupal\dh_certificate\ProgressManagerInterface;

/**
 * Service for managing certificate progress.
 */
class DHCertificateProgressService implements ProgressManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Constructs a new DHCertificateProgressService object.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    ConfigFactoryInterface $config_factory,
    LoggerChannelFactoryInterface $logger_factory = NULL
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
    $this->logger = $logger_factory ? $logger_factory->get('dh_certificate') : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserProgress(AccountInterface $account) {
    // Basic progress structure
    return [
      'total_courses' => 7,
      'completed_courses' => 3,
      'total_credits' => 9,
      'required_credits' => 12,
      'percentage' => 45,
      'courses' => [
        ['title' => 'DH 101'],
        ['title' => 'DH 201'],
        ['title' => 'HIST 301'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getUserProgressById($uid) {
    try {
      $user = $this->entityTypeManager->getStorage('user')->load($uid);
      if (!$user) {
        throw new \Exception('User not found');
      }
      return $this->getUserProgress($user);
    }
    catch (\Exception $e) {
      $this->logger->error('Error loading user progress: @message', [
        '@message' => $e->getMessage()
      ]);
      return [
        'total_courses' => 0,
        'completed_courses' => 0,
        'total_credits' => 0,
        'required_credits' => 12,
        'percentage' => 0,
        'courses' => [],
      ];
    }
  }

  public function calculateCompletion(UserInterface $user) {
    $requirements = $this->configFactory->get('dh_certificate.requirements');
    
    // Initialize completion tracking
    $completed = [
      'core_courses' => 0,
      'elective_credits' => 0,
      'completed_count' => 0,
      'total_count' => 0,
      'due_dates' => $this->getDueDates($requirements),
      'requirements_status' => [],
      'total_percentage' => 0,
    ];
    
    // Track core courses
    $core_courses = $requirements->get('core_courses') ?? [];
    try {
      $progress_entities = $this->entityTypeManager
        ->getStorage('certificate_progress')
        ->loadByProperties(['uid' => $user->id()]);
      
      $completed_courses = [];
      if (!empty($progress_entities)) {
        $progress = reset($progress_entities); // Get the first progress entity
        if ($progress && $progress->hasField('completed_courses')) {
          $completed_courses = $progress->get('completed_courses')->referencedEntities();
        }
      }
    }
    catch (\Exception $e) {
      $completed_courses = [];
    }

    foreach ($core_courses as $course_id) {
      $status = $this->getCourseStatus($course_id, $completed_courses);
      $completed['requirements_status'][] = [
        'label' => $this->getCourseLabel($course_id),
        'status' => $status,
        'type' => 'core',
      ];
      if ($status === 'complete') {
        $completed['core_courses']++;
        $completed['completed_count']++;
      }
      $completed['total_count']++;
    }

    // Calculate overall percentage
    if ($completed['total_count'] > 0) {
      $completed['total_percentage'] = round(($completed['completed_count'] / $completed['total_count']) * 100);
    }
    
    return $completed;
  }

  protected function getDueDates($requirements) {
    $dates = [];
    
    $dueDate = $requirements->get('due_date');
    if ($dueDate !== NULL) {
      $dates['certificate'] = [
        'type' => $dueDate['type'] ?? 'academic',
        'value' => $dueDate['value'] ?? '',
        'format' => $dueDate['format'] ?? 'term-year',
      ];
    }

    $additionalRequirements = $requirements->get('additional_requirements') ?? [];
    foreach ($additionalRequirements as $req) {
      if (!empty($req['config']['due_date'])) {
        $dates[$req['id']] = [
          'type' => $req['config']['due_date']['type'] ?? 'academic',
          'value' => $req['config']['due_date']['value'] ?? '',
          'format' => $req['config']['due_date']['format'] ?? 'term-year',
        ];
      }
    }

    return $dates;
  }

  protected function formatDueDate($date) {
    if ($date['type'] === 'calendar') {
      return date($date['format'], strtotime($date['value']));
    }
    return $date['value']; // For academic terms, return as-is
  }

  protected function createUserProgress($uid) {
    $progress = $this->entityTypeManager->getStorage('certificate_progress')->create([
      'uid' => $uid,
    ]);
    $progress->save();
    return $progress;
  }

  protected function getCourseStatus($course_id, $completed_courses) {
    foreach ($completed_courses as $course) {
      if ($course->id() == $course_id) {
        return 'complete';
      }
    }
    // Here you could add logic to detect 'in_progress' status
    return 'pending';
  }

  protected function getCourseLabel($course_id) {
    $node = $this->entityTypeManager->getStorage('node')->load($course_id);
    return $node ? $node->label() : $course_id;
  }

  protected function logDebug($message, array $context = []) {
    if ($this->logger) {
      $this->logger->debug($message, $context);
    }
  }

  protected function logError($message, array $context = []) {
    if ($this->logger) {
      $this->logger->error($message, $context);
    }
  }

  /**
   * Calculate total credits completed by a user.
   *
   * @param int $uid
   *   The user ID.
   *
   * @return int
   *   The total number of credits completed.
   */
  public function calculateTotalCredits($uid) {
    try {
      $query = $this->entityTypeManager->getStorage('course_enrollment')->getQuery()
        ->condition('uid', $uid)
        ->condition('status', 'completed')
        ->accessCheck(FALSE);
      
      $enrollments = $query->execute();
      
      $total_credits = 0;
      if (!empty($enrollments)) {
        $courses = $this->entityTypeManager->getStorage('node')->loadMultiple($enrollments);
        foreach ($courses as $course) {
          if ($course->hasField('field_credits')) {
            $total_credits += (int) $course->get('field_credits')->value;
          }
        }
      }
      
      return $total_credits;
    }
    catch (\Exception $e) {
      $this->logError('Error calculating credits: @message', [
        '@message' => $e->getMessage()
      ]);
      return 0;
    }
  }
}
