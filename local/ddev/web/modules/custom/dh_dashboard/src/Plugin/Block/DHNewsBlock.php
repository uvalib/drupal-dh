<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Provides a DH News Block.
 *
 * @Block(
 *   id = "dh_dashboard_news",
 *   admin_label = @Translation("DH News"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DHNewsBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $config = \Drupal::config('dh_dashboard.settings');
        $show_debug = $config->get('show_debug') ?: FALSE;
        $news_items_per_page = (int) $config->get('news_items_per_page');

        return [
            '#theme' => 'dh_dashboard_news',
            '#news' => $this->getNews(),
            '#show_debug' => $show_debug,
            '#items_per_page' => $news_items_per_page,
            '#attributes' => new Attribute(['class' => ['block-dh-dashboard-news']]),
            '#attached' => [
                'library' => ['dh_dashboard/dashboard'],
                'drupalSettings' => [
                    'dhDashboard' => [
                        'items_per_page' => $news_items_per_page ?: 3, // Fallback to 3 if config is empty
                    ],
                ],
            ],
            '#cache' => ['max-age' => 0],
        ];
    }

    protected function getNews()
    {
        return [
        'items' => [
        [
          'title' => 'New Digital Archives Course Available',
          'date' => '2024-02-15',
          'summary' => 'Explore digital preservation techniques in our new course DH 305: Digital Archives and Preservation.',
          'category' => 'courses',
          'priority' => 'high',
          'category_class' => 'news-category--courses',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'graduation-cap',
        ],
        [
          'title' => 'Summer Digital Humanities Workshop Series',
          'date' => '2024-02-10',
          'summary' => 'Register now for our intensive summer workshops covering text analysis, data visualization, and digital mapping.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'calendar-alt',
        ],
        [
          'title' => 'Certificate Requirements Updated',
          'date' => '2024-02-01',
          'summary' => 'New elective options added for the 2024-25 academic year. Check your progress dashboard for details.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'certificate',
        ],
        [
          'title' => 'Student Project Showcase',
          'date' => '2024-01-28',
          'summary' => 'View outstanding digital humanities projects from this semester\'s graduating cohort.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'project-diagram',
        ],
        [
          'title' => 'New Data Visualization Workshop Series',
          'date' => '2024-02-15',
          'summary' => 'Join us for a six-week workshop series on advanced data visualization techniques.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'chart-bar',
        ],
        [
          'title' => 'Digital Archives Conference 2024',
          'date' => '2024-02-10',
          'summary' => 'Annual conference on digital preservation and archival practices.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'archive',
        ],
        [
          'title' => 'Spring Course Registration Open',
          'date' => '2024-02-05',
          'summary' => 'Registration for spring semester courses is now available.',
          'category' => 'courses',
          'priority' => 'high',
          'category_class' => 'news-category--courses',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'book-open',
        ],
        [
          'title' => 'New Text Analysis Tools Available',
          'date' => '2024-02-01',
          'summary' => 'Access to new computational linguistics tools now available to all students.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'tools',
        ],
        [
          'title' => 'Guest Lecture: AI in Humanities',
          'date' => '2024-01-28',
          'summary' => 'Distinguished speaker series presents "AI Applications in Humanities Research".',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'robot',
        ],
        [
          'title' => 'Research Fellowship Opportunities',
          'date' => '2024-01-25',
          'summary' => 'Summer research fellowships now accepting applications.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'hand-holding-usd',
        ],
        [
          'title' => 'Digital Humanities Symposium',
          'date' => '2024-01-20',
          'summary' => 'Annual DH symposium featuring student research presentations.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'users',
        ],
        [
          'title' => 'New Course: Digital Storytelling',
          'date' => '2024-01-15',
          'summary' => 'Exciting new course offering for next semester.',
          'category' => 'courses',
          'priority' => 'high',
          'category_class' => 'news-category--courses',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'book',
        ],
        [
          'title' => 'Library Digital Resources Workshop',
          'date' => '2024-01-10',
          'summary' => 'Learn about new digital resources available through the library.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'university',
        ],
        [
          'title' => 'Certificate Program Updates',
          'date' => '2024-01-05',
          'summary' => 'Important changes to certificate requirements for 2024.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'certificate',
        ],
        [
          'title' => 'Virtual Reality Lab Opening',
          'date' => '2024-01-01',
          'summary' => 'New VR lab now available for student projects.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'vr-cardboard',
        ],
        [
          'title' => 'Summer Institute Applications',
          'date' => '2023-12-20',
          'summary' => 'Applications now open for summer DH institute.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'file-alt',
        ],
        [
          'title' => 'New Partnership with Digital Archive',
          'date' => '2023-12-15',
          'summary' => 'Exciting new collaboration with national digital archive.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'archive',
        ],
        [
          'title' => 'Python for Humanities Workshop',
          'date' => '2023-12-10',
          'summary' => 'Beginner-friendly programming workshop series.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'python',
        ],
        [
          'title' => 'Digital Mapping Project Showcase',
          'date' => '2023-12-05',
          'summary' => 'Student digital mapping projects presentation day.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'map',
        ],
        [
          'title' => 'End of Year Project Exhibition',
          'date' => '2023-12-01',
          'summary' => 'Showcase of student digital humanities projects.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'project-diagram',
        ],
        [
          'title' => 'New Media Lab Equipment',
          'date' => '2023-11-25',
          'summary' => 'Latest additions to our media lab resources.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'video',
        ],
        [
          'title' => 'Internship Opportunities',
          'date' => '2023-11-20',
          'summary' => 'Spring internship positions now available.',
          'category' => 'program',
          'priority' => 'high',
          'category_class' => 'news-category--program',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'briefcase',
        ],
        ],
        'attributes' => [
        'class' => ['dh-news-block', 'news-grid', 'block-spacing'],
        ],
        ];
    }
}