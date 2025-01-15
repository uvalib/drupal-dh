<?php

namespace Drupal\dh_dashboard\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure DH Dashboard settings.
 */
class DHDashboardSettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['dh_dashboard.settings'];
  }

  public function getFormId() {
    return 'dh_dashboard_settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dh_dashboard.settings');

    // General Settings
    $form['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General Settings'),
      '#open' => TRUE,
    ];

    $form['general']['show_debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show Debug Information'),
      '#default_value' => $config->get('show_debug'),
    ];

    $form['general']['dashboard_node'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#selection_settings' => ['target_bundles' => ['dh_dashboard']],
      '#title' => $this->t('Dashboard Node'),
      '#default_value' => $config->get('dashboard_node') ? 
        \Drupal::entityTypeManager()->getStorage('node')->load($config->get('dashboard_node')) : 
        NULL,
      '#description' => $this->t('Select the node to use as the dashboard.'),
      '#required' => TRUE,
    ];

    // Block Settings
    $form['blocks'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Block Settings'),
    ];

    // Events Block Settings
    $form['blocks']['events'] = [
      '#type' => 'details',
      '#title' => $this->t('Events Block'),
      '#group' => 'blocks',
    ];

    $events_settings = $config->get('blocks.events') ?: [];
    $form['blocks']['events']['items_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Items per page'),
      '#default_value' => $events_settings['items_per_page'] ?? 10,
      '#min' => 1,
      '#max' => 50,
    ];

    $form['blocks']['events']['event_type_filter'] = [
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

    $form['blocks']['events']['show_past_events'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show past events by default'),
      '#default_value' => $events_settings['show_past_events'] ?? TRUE,
    ];

    $form['blocks']['events']['display_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Display mode'),
      '#default_value' => $events_settings['display_mode'] ?? 'grid',
      '#options' => [
        'grid' => $this->t('Grid'),
        'list' => $this->t('List'),
      ],
    ];

    // Add similar sections for news and program info blocks...

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dh_dashboard.settings')
      // General settings
      ->set('show_debug', $form_state->getValue(['general', 'show_debug']))
      ->set('dashboard_node', $form_state->getValue(['general', 'dashboard_node']))
      // Block settings
      ->set('blocks.events', [
        'items_per_page' => $form_state->getValue(['blocks', 'events', 'items_per_page']),
        'event_type_filter' => $form_state->getValue(['blocks', 'events', 'event_type_filter']),
        'show_past_events' => $form_state->getValue(['blocks', 'events', 'show_past_events']),
        'display_mode' => $form_state->getValue(['blocks', 'events', 'display_mode']),
      ])
      ->save();

    parent::submitForm($form, $form_state);
  }
}
