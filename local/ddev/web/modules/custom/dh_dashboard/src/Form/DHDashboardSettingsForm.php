<?php

namespace Drupal\dh_dashboard\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Configure DH Dashboard settings.
 */
class DHDashboardSettingsForm extends ConfigFormBase
{

    /**
     * The messenger service.
     *
     * @var \Drupal\Core\Messenger\MessengerInterface
     */
    protected $messenger;

    /**
     * {@inheritdoc}
     */
    public function __construct(ConfigFactoryInterface $config_factory, MessengerInterface $messenger)
    {
        parent::__construct($config_factory);
        $this->messenger = $messenger;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('config.factory'),
            $container->get('messenger')
        );
    }

    protected function getEditableConfigNames()
    {
        return ['dh_dashboard.settings'];
    }

    public function getFormId()
    {
        return 'dh_dashboard_settings';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'dh_dashboard/admin-form';
    
        $config = $this->config('dh_dashboard.settings');

        // Display Settings
        $form['display_settings'] = [
        '#type' => 'details',
        '#title' => $this->t('Display Settings'),
        '#open' => true,
        ];

        $form['display_settings']['default_items_per_page'] = [
        '#type' => 'number',
        '#title' => $this->t('Default items per page'),
        '#description' => $this->t('Default number of items to display per page in dashboard blocks.'),
        '#default_value' => $config->get('default_items_per_page') ?? 3,
        '#min' => 1,
        '#max' => 50,
        ];

        $form['display_settings']['display_mode'] = [
        '#type' => 'select',
        '#title' => $this->t('Default display mode'),
        '#default_value' => $config->get('display_mode') ?? 'grid',
        '#options' => [
        'grid' => $this->t('Grid'),
        'list' => $this->t('List'),
        'cards' => $this->t('Cards'),
        ],
        ];

        // Debug Settings
        $form['debug_settings'] = [
        '#type' => 'details',
        '#title' => $this->t('Debug Settings'),
        '#open' => true,
        ];

        $form['debug_settings']['show_debug'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Show Debug Information'),
        '#description' => $this->t('Display debug information on the dashboard.'),
        '#default_value' => $config->get('show_debug') ?: false,
        ];

        // Block Settings
        $form['blocks'] = [
        '#type' => 'vertical_tabs',
        '#title' => $this->t('Block Settings'),
        ];

        // Events Block Settings
        $form['block_settings']['events'] = [
        '#type' => 'details',
        '#title' => $this->t('Events Block'),
        '#group' => 'blocks',
        ];

        $events_settings = $config->get('blocks.events') ?: [];
        $form['block_settings']['events']['items_per_page'] = [
        '#type' => 'number',
        '#title' => $this->t('Items per page'),
        '#default_value' => $events_settings['items_per_page'] ?? 3, // Changed from 10 to 3
        '#min' => 1,
        '#max' => 50,
        ];

        $form['block_settings']['events']['event_type_filter'] = [
        '#type' => 'select',
        '#title' => $this->t('Default event type filter'),
        '#default_value' => $events_settings['event_type_filter'] ?? 'all',
        '#options' => [
        'all' => $this->t('All types'),
        'conference' => $this->t('Conferences'),
        'workshop' => $this->t('Workshops'),
        'seminar' => $this->t('Seminars'),
        ],
        ];

        $form['block_settings']['events']['show_past_events'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Show past events by default'),
        '#default_value' => $events_settings['show_past_events'] ?? true,
        ];

        $form['block_settings']['events']['display_mode'] = [
        '#type' => 'select',
        '#title' => $this->t('Display mode'),
        '#default_value' => $events_settings['display_mode'] ?? 'grid',
        '#options' => [
        'grid' => $this->t('Grid'),
        'list' => $this->t('List'),
        ],
        ];

        // Add Content Settings
        $form['content_settings'] = [
            '#type' => 'details',
            '#title' => $this->t('Content Settings'),
            '#open' => true,
        ];

        // Get the dashboard node ID and load the node if it exists
        $node_id = $config->get('dashboard_node');
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        
        // Load the current dashboard node
        $default_node = NULL;
        if (!empty($node_id)) {
            $nodes = $node_storage->loadByProperties([
                'nid' => $node_id,
                'type' => 'dh_dashboard',
            ]);
            $default_node = reset($nodes);
        }
        
        // If no default node is set, try to find the one titled 'Default Dashboard'
        if (!$default_node) {
            $nodes = $node_storage->loadByProperties([
                'type' => 'dh_dashboard',
                'title' => 'Default Dashboard',
            ]);
            $default_node = reset($nodes);
            
            // Save this as the default if found
            if ($default_node) {
                $this->config('dh_dashboard.settings')
                    ->set('dashboard_node', $default_node->id())
                    ->save();
            }
        }

        $form['content_settings']['dashboard_node'] = [
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#selection_settings' => ['target_bundles' => ['dh_dashboard']],
            '#title' => $this->t('Dashboard Node'),
            '#default_value' => $default_node,
            '#description' => $this->t('Select the node to use as the dashboard.'),
            '#required' => TRUE,
        ];

        // Add similar sections for news and program info blocks...

        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Get values from flattened structure
        $default_items_per_page = (int) $form_state->getValue('default_items_per_page');
        $display_mode = $form_state->getValue('display_mode');
        $show_debug = (bool) $form_state->getValue('show_debug');

        // Get block settings from flattened structure
        $events_settings = [
        'items_per_page' => (int) $form_state->getValue('items_per_page'),
        'event_type_filter' => $form_state->getValue('event_type_filter'),
        'show_past_events' => (bool) $form_state->getValue('show_past_events'),
        'display_mode' => $form_state->getValue('display_mode'),
        ];

        // Add dashboard_node to the save operation
        $dashboard_node = $form_state->getValue('dashboard_node');
        if (empty($dashboard_node)) {
            $dashboard_node = $this->config('dh_dashboard.settings')->get('dashboard_node');
        }

        // Save configuration
        $this->config('dh_dashboard.settings')
            ->set('default_items_per_page', $default_items_per_page)
            ->set('display_mode', $display_mode)
            ->set('show_debug', $show_debug)
            ->set('blocks.events', $events_settings)
            ->set('dashboard_node', $dashboard_node)
            ->save();

        // Clear caches
        drupal_flush_all_caches();

        parent::submitForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);

        // Get values from flattened structure
        $default_items_per_page = $form_state->getValue('default_items_per_page');
        $items_per_page = $form_state->getValue('items_per_page');

        // Validate global items per page
        if (!is_numeric($default_items_per_page) || intval($default_items_per_page) < 1) {
            $form_state->setErrorByName('default_items_per_page', 
                $this->t('Global items per page must be at least 1.'));
        }

        // Validate events block items per page
        if (!is_numeric($items_per_page) || intval($items_per_page) < 1) {
            $form_state->setErrorByName('items_per_page', 
                $this->t('Events block items per page must be at least 1.'));
        }
    }
}
