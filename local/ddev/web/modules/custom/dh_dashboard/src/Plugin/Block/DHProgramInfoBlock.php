<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Template\Attribute;

/**
 * Provides a DH Program Information Block.
 *
 * @Block(
 *   id = "dh_dashboard_program_info",
 *   admin_label = @Translation("Program Information"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DHProgramInfoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $program_info = $this->getProgramInfo();
    
    return [
      '#theme' => 'dh_dashboard_program_info',
      '#program_info' => $program_info,
      '#attributes' => new Attribute($program_info['attributes'] ?? []),
      '#label_display' => 'FALSE',
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  protected function getProgramInfo() {
    return [
      'important_dates' => [
        [
          'title' => 'Application Deadline (Fall 2024)',
          'date' => '2024-04-15',
          'description' => 'Last day to submit certificate program applications',
          'icon' => 'calendar',
          'class' => 'date-item--deadline',
        ],
        [
          'title' => 'Summer Institute Registration',
          'date' => '2024-03-01',
          'description' => 'Registration opens for DH Summer Institute workshops',
          'icon' => 'calendar',
          'class' => 'date-item--registration',
        ],
        [
          'title' => 'Capstone Submission Deadline',
          'date' => '2024-05-10',
          'description' => 'Final day to submit capstone projects for Spring graduation',
          'icon' => 'calendar',
          'class' => 'date-item--submission',
        ],
      ],
      'resources' => [
        [
          'title' => 'Program Handbook',
          'url' => '/dh-certificate/handbook',
          'description' => 'Complete guide to program requirements and policies',
          'type' => 'document',
          'icon' => 'book',
          'class' => 'resource-item--document',
        ],
        [
          'title' => 'Course Schedule',
          'url' => '/dh-certificate/courses',
          'description' => 'Browse upcoming DH certificate courses',
          'type' => 'schedule',
          'icon' => 'calendar',
          'class' => 'resource-item--schedule',
        ],
        [
          'title' => 'Digital Tools Guide',
          'url' => '/dh-certificate/tools',
          'description' => 'Resources for recommended software and platforms',
          'type' => 'guide',
          'icon' => 'tools',
          'class' => 'resource-item--guide',
        ],
        [
          'title' => 'Academic Advising',
          'url' => '/dh-certificate/advising',
          'description' => 'Schedule a meeting with program advisors',
          'type' => 'contact',
          'icon' => 'user',
          'class' => 'resource-item--contact',
        ],
      ],
      'requirements_summary' => [
        'total_credits' => 15,
        'core_courses' => 4,
        'electives' => 2,
        'capstone' => 1,
        'time_limit' => '2 years',
      ],
      'attributes' => [
        'class' => ['dh-program-info', 'info-grid', 'block-spacing'],
      ],
    ];
  }
}
