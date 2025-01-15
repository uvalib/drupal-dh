<?php

namespace Drupal\dh_dashboard\Plugin\Block;

/**
 * Provides a news block for the Digital Humanities Dashboard.
 *
 * @Block(
 *   id = "dh_dashboard_news",
 *   admin_label = @Translation("DH News"),
 *   category = @Translation("DH Dashboard"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user", required = FALSE)
 *   }
 * )
 */
class DHNewsBlock extends DHDashboardBlockBase {

  protected function getThemeHook(): string {
    return 'dh_dashboard_news';
  }

  protected function getBlockClass(): string {
    return 'block-dh-dashboard-news';
  }

  protected function getItemsPerPageConfigKey(): string {
    return 'news_items_per_page';
  }

  protected function getDisplayModeConfigKey(): string {
    return 'news_display_mode';
  }

  protected function getItems(): array {
    return [
      'items' => [
        [
          'title' => 'New Digital Humanities Certificate Program',
          'date' => '2024-03-15',
          'summary' => 'Announcing our new certificate program in Digital Humanities, starting Fall 2024.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'graduation-cap',
        ],
        [
          'title' => 'New Faculty Research Symposium',
          'date' => '2024-03-07',
          'summary' => 'Join us for presentations of innovative DH research by our newest faculty members.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'chalkboard-teacher',
        ],
        [
          'title' => 'Summer Research Grants Available',
          'date' => '2024-03-06',
          'summary' => 'Applications now open for summer digital humanities research funding.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'dollar-sign',
        ],
        [
          'title' => 'Introduction to AI Tools Workshop',
          'date' => '2024-03-05',
          'summary' => 'Learn about responsible AI integration in humanities research.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'robot',
        ],
        [
          'title' => 'Fall Course Proposals Due',
          'date' => '2024-03-04',
          'summary' => 'Faculty deadline for submitting new DH course proposals.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'calendar-check',
        ],
        [
          'title' => 'Digital Preservation Workshop',
          'date' => '2024-03-03',
          'summary' => 'Best practices for preserving digital humanities projects.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'archive',
        ],
        [
          'title' => 'New Research Lab Opening',
          'date' => '2024-03-02',
          'summary' => 'State-of-the-art digital humanities research facility opening ceremony.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'flask',
        ],
        [
          'title' => 'Digital Publishing Seminar',
          'date' => '2024-03-01',
          'summary' => 'Exploring modern digital publishing platforms and methods.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'book',
        ],
        [
          'title' => 'Student Project Exhibition',
          'date' => '2024-02-29',
          'summary' => 'Showcase of innovative student digital humanities projects.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'project-diagram',
        ],
        [
          'title' => 'Digital Collections Workshop',
          'date' => '2024-02-28',
          'summary' => 'Managing and curating digital collections effectively.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'database',
        ],
        [
          'title' => 'Research Methods Symposium',
          'date' => '2024-02-27',
          'summary' => 'Advanced digital research methods in humanities.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'microscope',
        ],
        [
          'title' => 'Visualization Tools Workshop',
          'date' => '2024-02-26',
          'summary' => 'Hands-on training with data visualization tools.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'chart-line',
        ],
        [
          'title' => 'Digital Humanities Journal Launch',
          'date' => '2024-02-25',
          'summary' => 'Inaugural issue of our peer-reviewed digital journal.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'journal-whills',
        ],
        [
          'title' => 'GIS Workshop Series',
          'date' => '2024-02-24',
          'summary' => 'Introduction to geographical information systems in humanities.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'map-marked-alt',
        ],
        [
          'title' => 'Digital Archiving Symposium',
          'date' => '2024-02-23',
          'summary' => 'Latest developments in digital archiving practices.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'archive',
        ],
        [
          'title' => 'New Partnership Announcement',
          'date' => '2024-02-22',
          'summary' => 'Collaboration with National Digital Library Initiative.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'handshake',
        ],
        [
          'title' => 'Digital Curation Workshop',
          'date' => '2024-02-21',
          'summary' => 'Best practices for digital content curation.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'cubes',
        ],
        [
          'title' => 'Metadata Standards Forum',
          'date' => '2024-02-20',
          'summary' => 'Discussion of current metadata standards and practices.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'tags',
        ],
        [
          'title' => 'Digital Ethics Symposium',
          'date' => '2024-02-19',
          'summary' => 'Exploring ethical considerations in digital research.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'balance-scale',
        ],
        [
          'title' => 'New Software Resources',
          'date' => '2024-02-18',
          'summary' => 'Additional software licenses available for DH projects.',
          'category' => 'program',
          'priority' => 'medium',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'laptop-code',
        ],
        [
          'title' => 'Digital Exhibit Design Workshop',
          'date' => '2024-02-17',
          'summary' => 'Creating engaging online exhibitions and displays.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'images',
        ],
        [
          'title' => 'Research Infrastructure Update',
          'date' => '2024-02-16',
          'summary' => 'Major updates to our digital research infrastructure.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'network-wired',
        ],
        [
          'title' => 'Digital Humanities Mentorship Program',
          'date' => '2024-02-15',
          'summary' => 'New mentorship opportunities for DH students.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'users',
        ],
        [
          'title' => 'Web Archiving Workshop',
          'date' => '2024-02-14',
          'summary' => 'Introduction to web archiving tools and methods.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'globe',
        ],
      ],
      'attributes' => [
        'class' => ['dh-news-block', 'news-grid', 'block-spacing'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    
    // Get user context
    $user = $this->getContextValue('user') ?: $this->currentUser;
    $user_data = $this->getUserData($user);
    
    // Add user data to build array
    $build['#user'] = $user_data;
    $build['#news'] = $this->getItems();

    return $build;
  }

  /**
   * Gets formatted user data for display.
   */
  protected function getUserData($account) {
    // ... existing getUserData implementation ...
  }
}