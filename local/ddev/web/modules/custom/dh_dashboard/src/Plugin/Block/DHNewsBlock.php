<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a news block for the Digital Humanities Dashboard.
 *
 * This block displays a configurable list of news items related to
 * Digital Humanities courses, events, and program updates.
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
class DHNewsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a DHNewsBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configFactory->get('dh_dashboard.settings');
    $show_debug = $config->get('show_debug') ?: FALSE;
    $news_items_per_page = (int) $config->get('news_items_per_page');
    $display_mode = $config->get('news_display_mode') ?: 'grid';

    // Get the user context if available
    $user = $this->getContextValue('user') ?: $this->currentUser;
    $user_data = $this->getUserData($user);

    return [
      '#theme' => 'dh_dashboard_news',
      '#news' => $this->getNews(),
      '#user' => $user_data,
      '#show_debug' => $show_debug,
      '#items_per_page' => $news_items_per_page,
      '#display_mode' => $display_mode,
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

  /**
   * Gets formatted user data for display.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   *
   * @return array
   *   Formatted user data.
   */
  protected function getUserData(AccountInterface $account) {
    $user_data = [
      'name' => $account->getDisplayName(),
      'roles' => array_diff($account->getRoles(), ['authenticated']),
      'picture' => NULL,
    ];

    // Load the full user entity to get the picture
    if ($user = \Drupal::entityTypeManager()->getStorage('user')->load($account->id())) {
      if ($user->hasField('user_picture') && !$user->get('user_picture')->isEmpty()) {
        $user_data['picture'] = $user->get('user_picture')->view([
          'label' => 'hidden',
          'type' => 'image',
          'settings' => [
            'image_style' => 'thumbnail',
          ],
        ]);
      }
    }

    return $user_data;
  }

  protected function getNews() {
    return [
      'items' => [
        [
          'title' => 'Annual Digital Humanities Conference 2024',
          'date' => '2024-03-15',
          'summary' => 'Join us for our flagship conference featuring keynote speakers and innovative DH research presentations.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'users-class',
        ],
        [
          'title' => 'Digital Archives Workshop Series',
          'date' => '2024-03-14',
          'summary' => 'Four-week intensive workshop on digital preservation techniques.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'archive',
        ],
        // Add 29 more similar events with different dates and details
        [
          'title' => 'Text Analysis Symposium',
          'date' => '2024-03-13',
          'summary' => 'Explore computational methods in textual analysis.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'file-alt',
        ],
        [
          'title' => 'Digital Mapping Workshop',
          'date' => '2024-03-12',
          'summary' => 'Learn GIS techniques for historical mapping projects.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'map',
        ],
        [
          'title' => 'Virtual Reality in Humanities Lecture',
          'date' => '2024-03-11',
          'summary' => 'Guest lecture on VR applications in humanities research.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'vr-cardboard',
        ],
        [
          'title' => 'Data Visualization Workshop',
          'date' => '2024-03-10',
          'summary' => 'Hands-on training with visualization tools.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'chart-bar',
        ],
        [
          'title' => 'Digital Pedagogy Forum',
          'date' => '2024-03-09',
          'summary' => 'Discussion of digital tools in teaching humanities.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'chalkboard-teacher',
        ],
        [
          'title' => 'Python for Humanists',
          'date' => '2024-03-08',
          'summary' => 'Beginner-friendly programming workshop.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'code',
        ],
        [
          'title' => 'Digital Collections Symposium',
          'date' => '2024-03-07',
          'summary' => 'Exploring digital collection management.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'database',
        ],
        [
          'title' => 'Social Media Analysis Workshop',
          'date' => '2024-03-06',
          'summary' => 'Methods for analyzing social media data.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'share-alt',
        ],
        [
          'title' => 'Digital Publishing Workshop',
          'date' => '2024-03-05',
          'summary' => 'Learn about digital publishing platforms.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'book',
        ],
        [
          'title' => 'Text Mining Workshop',
          'date' => '2024-03-04',
          'summary' => 'Introduction to text mining techniques.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'search',
        ],
        [
          'title' => 'Digital Art Exhibition',
          'date' => '2024-03-03',
          'summary' => 'Showcase of digital art projects.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'palette',
        ],
        [
          'title' => 'Data Ethics Symposium',
          'date' => '2024-03-02',
          'summary' => 'Discussion of ethical issues in digital research.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'balance-scale',
        ],
        [
          'title' => 'Digital Storytelling Workshop',
          'date' => '2024-03-01',
          'summary' => 'Create compelling digital narratives.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'film',
        ],
        [
          'title' => '3D Modeling Workshop',
          'date' => '2024-02-29',
          'summary' => 'Learn 3D modeling for cultural heritage.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'cube',
        ],
        [
          'title' => 'Digital Archaeology Lecture',
          'date' => '2024-02-28',
          'summary' => 'Latest methods in digital archaeology.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'dig',
        ],
        [
          'title' => 'Network Analysis Workshop',
          'date' => '2024-02-27',
          'summary' => 'Learn network analysis techniques.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'project-diagram',
        ],
        [
          'title' => 'Digital Archives Roundtable',
          'date' => '2024-02-26',
          'summary' => 'Discussion of archival best practices.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'folder-open',
        ],
        [
          'title' => 'Computational Linguistics Forum',
          'date' => '2024-02-25',
          'summary' => 'Exploring language processing tools.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'language',
        ],
        [
          'title' => 'Digital Heritage Symposium',
          'date' => '2024-02-24',
          'summary' => 'Preserving cultural heritage digitally.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'landmark',
        ],
        [
          'title' => 'Web Development Workshop',
          'date' => '2024-02-23',
          'summary' => 'Basic web development for humanists.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'code',
        ],
        [
          'title' => 'Digital Museums Conference',
          'date' => '2024-02-22',
          'summary' => 'Future of digital museum experiences.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'university',
        ],
        [
          'title' => 'XML Workshop',
          'date' => '2024-02-21',
          'summary' => 'Working with XML in humanities projects.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'code',
        ],
        [
          'title' => 'Digital Library Forum',
          'date' => '2024-02-20',
          'summary' => 'Digital library management strategies.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'book-reader',
        ],
        [
          'title' => 'Metadata Workshop',
          'date' => '2024-02-19',
          'summary' => 'Best practices for metadata management.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'tags',
        ],
        [
          'title' => 'Digital History Symposium',
          'date' => '2024-02-18',
          'summary' => 'New methods in digital historical research.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'history',
        ],
        [
          'title' => 'OCR Workshop',
          'date' => '2024-02-17',
          'summary' => 'Working with OCR technologies.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'eye',
        ],
        [
          'title' => 'Digital Exhibition Design',
          'date' => '2024-02-16',
          'summary' => 'Creating engaging digital exhibitions.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'images',
        ],
        [
          'title' => 'Audio Processing Workshop',
          'date' => '2024-02-15',
          'summary' => 'Digital audio analysis techniques.',
          'category' => 'events',
          'priority' => 'medium',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--medium',
          'icon' => 'music',
        ],
        [
          'title' => 'Machine Learning in DH',
          'date' => '2024-02-14',
          'summary' => 'Applications of ML in humanities research.',
          'category' => 'events',
          'priority' => 'high',
          'category_class' => 'news-category--events',
          'priority_class' => 'priority-indicator--high',
          'icon' => 'brain',
        ],
      ],
      'attributes' => [
        'class' => ['dh-news-block', 'news-grid', 'block-spacing'],
      ],
    ];
  }
}