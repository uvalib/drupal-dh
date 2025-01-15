<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an events block for the Digital Humanities Dashboard.
 *
 * @Block(
 *   id = "dh_dashboard_events",
 *   admin_label = @Translation("DH Events"),
 *   category = @Translation("DH Dashboard"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user", required = FALSE)
 *   }
 * )
 */
class DHEventsBlock extends DHDashboardBlockBase {

  protected function getThemeHook(): string {
    return 'dh_dashboard_events';
  }

  protected function getBlockClass(): string {
    return 'block-dh-dashboard-events dh-dashboard-block';
  }

  protected function getItemsPerPageConfigKey(): string {
    return 'events_items_per_page';
  }

  protected function getDisplayModeConfigKey(): string {
    return 'events_display_mode';
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'event_type_filter' => 'all',
      'show_past_events' => TRUE,
      'items_per_page' => 3, // Default to 3 items per page
      'display_mode' => 'grid',
      'show_debug' => FALSE,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['event_type_filter'] = [
      '#type' => 'select',
      '#title' => $this->t('Event type filter'),
      '#default_value' => $config['event_type_filter'],
      '#options' => [
        'all' => $this->t('All types'),
        'conference' => $this->t('Conferences'),
        'workshop' => $this->t('Workshops'),
        'seminar' => $this->t('Seminars'),
      ],
    ];

    $form['show_past_events'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show past events'),
      '#default_value' => $config['show_past_events'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['event_type_filter'] = $form_state->getValue('event_type_filter');
    $this->configuration['show_past_events'] = $form_state->getValue('show_past_events');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get settings from config factory
    $config = $this->configFactory->get('dh_dashboard.settings');
    $block_settings = $config->get('blocks.events');
    
    // Merge block settings with configuration
    $this->configuration = array_merge($this->configuration, [
      'event_type_filter' => $block_settings['event_type_filter'] ?? $this->configuration['event_type_filter'],
      'show_past_events' => $block_settings['show_past_events'] ?? $this->configuration['show_past_events'],
      'items_per_page' => $block_settings['items_per_page'] ?? $this->configuration['items_per_page'],
      'display_mode' => $block_settings['display_mode'] ?? $this->configuration['display_mode'],
      'show_debug' => $config->get('show_debug') ?? $this->configuration['show_debug'],
    ]);

    // Get items with pagination
    $items = $this->getItems();
    $page_size = $this->configuration['items_per_page'] ?? 3;
    $items['items'] = array_slice($items['items'], 0, $page_size);
    
    return [
      '#theme' => $this->getThemeHook(),
      '#items' => $items,
      '#attributes' => new \Drupal\Core\Template\Attribute([
        'class' => ['dh-events-block', 'block-spacing'],
      ]),
      '#cache' => ['max-age' => 0],
      '#show_debug' => $config->get('show_debug'),
    ];
  }

  protected function getItems(): array {
    $config = $this->getConfiguration();
    $all_items = [
      'items' => [
        [
          'title' => 'Digital Methods Workshop Series',
          'date' => '2024-03-20',
          'description' => 'Hands-on workshops covering various digital research methods including text analysis, data visualization, and network analysis.',
          'location' => 'Digital Lab Room 101',
          'type' => 'workshop',
          'priority' => 'high',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'laptop-code',
          'url' => '/events/digital-methods-2024',
        ],
        [
          'title' => 'DH Spring Conference',
          'date' => '2024-04-15',
          'description' => 'Annual digital humanities research conference featuring keynote speakers, workshops, and student presentations.',
          'location' => 'University Conference Center',
          'type' => 'conference',
          'priority' => 'high',
          'category_class' => 'event-category--conference',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'users',
          'url' => '/events/spring-conference-2024',
        ],
        [
          'title' => 'AI in Humanities Research Seminar',
          'date' => '2024-03-22',
          'description' => 'Discussion of AI applications in humanities research with focus on ethical considerations.',
          'location' => 'Virtual Meeting Room',
          'type' => 'seminar',
          'priority' => 'high',
          'category_class' => 'event-category--seminar',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'robot',
          'url' => '/events/ai-humanities-2024',
        ],
        [
          'title' => 'Digital Archives Workshop',
          'date' => '2024-03-25',
          'description' => 'Best practices for digital preservation and archive management.',
          'location' => 'Library Room 202',
          'type' => 'workshop',
          'priority' => 'medium',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'archive',
          'url' => '/events/archives-workshop-2024',
        ],
        [
          'title' => 'Text Mining Symposium',
          'date' => '2024-04-01',
          'description' => 'Advanced techniques in text mining and natural language processing.',
          'location' => 'Computer Science Building',
          'type' => 'conference',
          'priority' => 'high',
          'category_class' => 'event-category--conference',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'file-alt',
          'url' => '/events/text-mining-2024',
        ],
        [
          'title' => 'Digital Pedagogy Workshop',
          'date' => '2024-04-05',
          'description' => 'Innovative teaching methods using digital tools.',
          'location' => 'Education Building 305',
          'type' => 'workshop',
          'priority' => 'medium',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'chalkboard-teacher',
          'url' => '/events/digital-pedagogy-2024',
        ],
        [
          'title' => 'Data Visualization Seminar',
          'date' => '2024-04-10',
          'description' => 'Creating effective visualizations for humanities data.',
          'location' => 'Digital Lab Room 103',
          'type' => 'seminar',
          'priority' => 'medium',
          'category_class' => 'event-category--seminar',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'chart-bar',
          'url' => '/events/data-viz-2024',
        ],
        [
          'title' => 'Digital Publishing Conference',
          'date' => '2024-04-20',
          'description' => 'Future of academic publishing in digital formats.',
          'location' => 'University Press Building',
          'type' => 'conference',
          'priority' => 'high',
          'category_class' => 'event-category--conference',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'book',
          'url' => '/events/publishing-2024',
        ],
        [
          'title' => 'Network Analysis Workshop',
          'date' => '2024-04-25',
          'description' => 'Social network analysis methods for humanities research.',
          'location' => 'Digital Lab Room 104',
          'type' => 'workshop',
          'priority' => 'medium',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'project-diagram',
          'url' => '/events/network-analysis-2024',
        ],
        [
          'title' => 'Digital Collections Seminar',
          'date' => '2024-05-01',
          'description' => 'Managing and curating digital collections.',
          'location' => 'Library Special Collections',
          'type' => 'seminar',
          'priority' => 'medium',
          'category_class' => 'event-category--seminar',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'database',
          'url' => '/events/collections-2024',
        ],
        [
          'title' => 'Summer Institute Planning',
          'date' => '2024-05-05',
          'description' => 'Planning session for upcoming DH Summer Institute.',
          'location' => 'Faculty Commons',
          'type' => 'seminar',
          'priority' => 'high',
          'category_class' => 'event-category--seminar',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'calendar-alt',
          'url' => '/events/summer-planning-2024',
        ],
        [
          'title' => 'GIS Methods Workshop',
          'date' => '2024-05-10',
          'description' => 'Introduction to geographical information systems.',
          'location' => 'Geography Lab',
          'type' => 'workshop',
          'priority' => 'medium',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'map-marked-alt',
          'url' => '/events/gis-2024',
        ],
        [
          'title' => 'Digital Storytelling Conference',
          'date' => '2024-05-15',
          'description' => 'Narrative techniques in digital environments.',
          'location' => 'Media Arts Center',
          'type' => 'conference',
          'priority' => 'high',
          'category_class' => 'event-category--conference',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'film',
          'url' => '/events/storytelling-2024',
        ],
        [
          'title' => 'Research Data Management',
          'date' => '2024-05-20',
          'description' => 'Best practices for managing research data.',
          'location' => 'Digital Lab Room 105',
          'type' => 'workshop',
          'priority' => 'medium',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'database',
          'url' => '/events/data-management-2024',
        ],
        [
          'title' => 'Digital Ethics Symposium',
          'date' => '2024-05-25',
          'description' => 'Ethical considerations in digital research.',
          'location' => 'Philosophy Department',
          'type' => 'conference',
          'priority' => 'high',
          'category_class' => 'event-category--conference',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'balance-scale',
          'url' => '/events/ethics-2024',
        ],
        [
          'title' => 'Web Development Workshop',
          'date' => '2024-06-01',
          'description' => 'Basic web development skills for humanists.',
          'location' => 'Digital Lab Room 106',
          'type' => 'workshop',
          'priority' => 'medium',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'code',
          'url' => '/events/web-dev-2024',
        ],
        [
          'title' => 'Digital Art History Seminar',
          'date' => '2024-06-05',
          'description' => 'Digital methods in art historical research.',
          'location' => 'Art History Department',
          'type' => 'seminar',
          'priority' => 'medium',
          'category_class' => 'event-category--seminar',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'palette',
          'url' => '/events/art-history-2024',
        ],
        [
          'title' => 'Computational Analysis Workshop',
          'date' => '2024-06-10',
          'description' => 'Introduction to computational methods.',
          'location' => 'Computer Lab 201',
          'type' => 'workshop',
          'priority' => 'medium',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'calculator',
          'url' => '/events/computational-2024',
        ],
        [
          'title' => 'Digital Archaeology Conference',
          'date' => '2024-06-15',
          'description' => 'Digital methods in archaeological research.',
          'location' => 'Archaeology Building',
          'type' => 'conference',
          'priority' => 'high',
          'category_class' => 'event-category--conference',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'dig',
          'url' => '/events/archaeology-2024',
        ],
        [
          'title' => 'Metadata Standards Workshop',
          'date' => '2024-06-20',
          'description' => 'Best practices for metadata creation.',
          'location' => 'Library Training Room',
          'type' => 'workshop',
          'priority' => 'medium',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'tags',
          'url' => '/events/metadata-2024',
        ],
        [
          'title' => 'Digital Music Analysis Seminar',
          'date' => '2024-06-25',
          'description' => 'Computational approaches to musicology.',
          'location' => 'Music Building',
          'type' => 'seminar',
          'priority' => 'medium',
          'category_class' => 'event-category--seminar',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'music',
          'url' => '/events/music-analysis-2024',
        ],
        [
          'title' => '3D Modeling Workshop',
          'date' => '2024-06-30',
          'description' => 'Creating 3D models for cultural heritage.',
          'location' => 'Digital Lab Room 107',
          'type' => 'workshop',
          'priority' => 'medium',
          'category_class' => 'event-category--workshop',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'cube',
          'url' => '/events/3d-modeling-2024',
        ],
        [
          'title' => 'Digital Literature Conference',
          'date' => '2024-07-05',
          'description' => 'Exploring digital approaches to literary analysis.',
          'location' => 'English Department',
          'type' => 'conference',
          'priority' => 'high',
          'category_class' => 'event-category--conference',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'book-reader',
          'url' => '/events/literature-2024',
        ],
      ],
      'attributes' => [
        'class' => ['dh-events-block', 'events-grid', 'block-spacing'],
      ],
    ];

    // Filter by event type if needed
    if ($config['event_type_filter'] !== 'all') {
      $all_items['items'] = array_filter($all_items['items'], function($item) use ($config) {
        return $item['type'] === $config['event_type_filter'];
      });
    }

    // Filter out past events if needed
    if (!$config['show_past_events']) {
      $today = date('Y-m-d');
      $all_items['items'] = array_filter($all_items['items'], function($item) use ($today) {
        return $item['date'] >= $today;
      });
    }

    return $all_items;
  }
}
