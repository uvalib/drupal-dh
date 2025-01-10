<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

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
    return [
      '#theme' => 'dh_dashboard_program_info',
      '#program_info' => $this->getProgramInfo(),
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
        ],
        [
          'title' => 'Summer Institute Registration',
          'date' => '2024-03-01',
          'description' => 'Registration opens for DH Summer Institute workshops',
        ],
        [
          'title' => 'Capstone Submission Deadline',
          'date' => '2024-05-10',
          'description' => 'Final day to submit capstone projects for Spring graduation',
        ],
      ],
      'resources' => [
        [
          'title' => 'Program Handbook',
          'url' => '/dh-certificate/handbook',
          'description' => 'Complete guide to program requirements and policies',
          'type' => 'document',
        ],
        [
          'title' => 'Course Schedule',
          'url' => '/dh-certificate/courses',
          'description' => 'Browse upcoming DH certificate courses',
          'type' => 'schedule',
        ],
        [
          'title' => 'Digital Tools Guide',
          'url' => '/dh-certificate/tools',
          'description' => 'Resources for recommended software and platforms',
          'type' => 'guide',
        ],
        [
          'title' => 'Academic Advising',
          'url' => '/dh-certificate/advising',
          'description' => 'Schedule a meeting with program advisors',
          'type' => 'contact',
        ],
      ],
      'requirements_summary' => [
        'total_credits' => 15,
        'core_courses' => 4,
        'electives' => 2,
        'capstone' => 1,
        'time_limit' => '2 years',
      ],
    ];
  }
}
