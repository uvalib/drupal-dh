<?php

namespace Drupal\dh_dashboard\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Configure DH Dashboard settings.
 */
class DashboardSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dh_dashboard.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dh_dashboard_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'dh_dashboard/admin-form';
    
    $config = $this->config('dh_dashboard.settings');

    $form['show_debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show Debug Information'),
      '#description' => $this->t('Display debug information on the dashboard.'),
      '#default_value' => $config->get('show_debug') ?? FALSE,
    ];

    $form['news_items'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of news items to display'),
      '#default_value' => $config->get('news_items') ?? 5,
      '#min' => 1,
      '#max' => 20,
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

    $form['dashboard_node'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#selection_settings' => ['target_bundles' => ['dh_dashboard']],
      '#title' => $this->t('Dashboard Node'),
      '#default_value' => $default_node,
      '#description' => $this->t('Select the node to use as the dashboard.'),
      '#required' => TRUE,
    ];

    $form = parent::buildForm($form, $form_state);
    
    // Add dashboard link to the actions group
    $form['actions']['dashboard'] = [
      '#type' => 'link',
      '#title' => $this->t('Go to Dashboard'),
      '#url' => Url::fromRoute('dh_dashboard.main'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
      '#weight' => 5, // Places it after the submit button
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    
    // Only validate dashboard_node if it was changed
    $dashboard_node = $form_state->getValue('dashboard_node');
    $old_node = $this->config('dh_dashboard.settings')->get('dashboard_node');
    
    if (empty($dashboard_node) && empty($old_node)) {
      $form_state->setError($form['dashboard_node'], $this->t('Dashboard Node is required when not previously set.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $dashboard_node = $form_state->getValue('dashboard_node');
    
    // If no new value was submitted, retain the existing value
    if (empty($dashboard_node)) {
      $dashboard_node = $this->config('dh_dashboard.settings')->get('dashboard_node');
    }
    
    $this->config('dh_dashboard.settings')
      ->set('show_debug', $form_state->getValue('show_debug'))
      ->set('news_items', $form_state->getValue('news_items'))
      ->set('dashboard_node', $dashboard_node)
      ->save();

    parent::submitForm($form, $form_state);
  }
}
