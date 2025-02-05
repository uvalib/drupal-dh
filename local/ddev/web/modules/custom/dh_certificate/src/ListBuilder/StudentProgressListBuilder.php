<?php

namespace Drupal\dh_certificate\ListBuilder;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\dh_certificate\Progress\ProgressManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * List builder for student progress.
 */
class StudentProgressListBuilder extends CertificateListBuilderBase {

  protected $progressManager;

  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('dh_certificate.progress')
    );
  }

  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, ProgressManagerInterface $progress_manager) {
    parent::__construct($entity_type, $storage);
    $this->progressManager = $progress_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [
      'student' => $this->t('Student'),
      'overall_progress' => [
        'data' => $this->t('Overall Progress'),
        'colspan' => 2,
      ],
    ];

    // Add columns for different progress types
    $header['course_progress'] = [
      'data' => $this->t('Course Progress'),
      'colspan' => 2,
    ];
    $header['project_progress'] = [
      'data' => $this->t('Project Progress'),
      'colspan' => 2,
    ];
    $header['requirements_progress'] = [
      'data' => $this->t('Requirements Met'),
      'colspan' => 2,
    ];

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $progress = $this->progressManager->getStudentProgress($entity->getOwner());
    
    return [
      'student' => $entity->getOwner()->getDisplayName(),
      'overall_progress' => [
        'percentage' => $this->formatPercentage($progress['overall_percentage']),
        'status' => $this->getProgressStatus($progress['overall_percentage']),
      ],
      'course_progress' => [
        'completed' => $this->t('@completed/@total courses', [
          '@completed' => $progress['completed_courses'],
          '@total' => $progress['total_courses'],
        ]),
        'credits' => $this->t('@earned/@required credits', [
          '@earned' => $progress['completed_credits'],
          '@required' => $progress['required_credits'],
        ]),
      ],
      'project_progress' => [
        'status' => $progress['project_status'] ?? $this->t('Not started'),
        'due_date' => $progress['project_due_date'] ?? '',
      ],
      'requirements_progress' => [
        'met' => $this->t('@met/@total', [
          '@met' => $progress['requirements_met'],
          '@total' => $progress['total_requirements'],
        ]),
        'remaining' => $this->formatRemainingRequirements($progress['remaining_requirements']),
      ],
    ];
  }

  protected function formatPercentage($percentage) {
    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => $this->t('@percent%', ['@percent' => round($percentage)]),
      '#attributes' => [
        'class' => ['progress-percentage'],
      ],
    ];
  }

  protected function formatRemainingRequirements(array $remaining) {
    if (empty($remaining)) {
      return $this->t('All requirements met');
    }

    return [
      '#theme' => 'item_list',
      '#items' => $remaining,
      '#attributes' => ['class' => ['remaining-requirements']],
    ];
  }

  protected function getProgressStatus($percentage) {
    if ($percentage >= 100) {
      return $this->t('Complete');
    }
    elseif ($percentage > 0) {
      return $this->t('In Progress');
    }
    return $this->t('Not Started');
  }

  protected function getListTheme() {
    return 'dh_certificate_student_progress_list';
  }
}
