<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Template\Attribute;

/**
 * Provides a DH Certificate Progress Block.
 *
 * @Block(
 *   id = "dh_dashboard_progress",
 *   admin_label = @Translation("Certificate Progress"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DHProgressBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $progress = $this->getProgress();
    
    return [
      '#theme' => 'dh_dashboard_progress',
      '#progress' => $progress,
      '#attributes' => new Attribute(['class' => ['dh-progress-block']]),
      '#label_display' => 'FALSE',
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  protected function getProgress() {
    // Calculate total completed based on course statuses
    $courses = [
      [
        'name' => 'Introduction to Digital Humanities',
        'status' => 'completed',
        'status_class' => 'course-status--completed',
        'type' => 'required',
        'icon' => 'check-circle',
      ],
      [
        'name' => 'Data Visualization Methods',
        'status' => 'in_progress',
        'status_class' => 'course-status--in-progress',
        'type' => 'core',
        'icon' => 'clock',
      ],
      [
        'name' => 'Digital Archives and Preservation',
        'status' => 'in_progress',
        'status_class' => 'course-status--in-progress',
        'type' => 'required',
        'icon' => 'clock',
      ],
      [
        'name' => 'Advanced Text Analysis',
        'status' => 'not_started',
        'status_class' => 'course-status--not-started',
        'type' => 'elective',
        'icon' => 'circle',
      ],
    ];

    $completed = count(array_filter($courses, function($course) {
      return $course['status'] === 'completed';
    }));

    return [
      'completed' => $completed,
      'total' => count($courses),
      'percentage' => round(($completed / count($courses)) * 100),
      'status_class' => 'progress-status--in-progress',
      'courses' => $courses,
      'attributes' => [
        'class' => ['dh-progress-block', 'block-spacing', 'single-card'],
      ],
      'title' => 'Certificate Progress',
    ];
  }
}