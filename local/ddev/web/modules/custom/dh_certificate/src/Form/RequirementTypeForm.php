<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form for creating and editing requirement types.
 */
class RequirementTypeForm extends FormBase {

  public function getFormId() {
    return 'requirement_type_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['type'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Requirement Type Details'),
    ];

    $form['type']['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#required' => TRUE,
      '#description' => $this->t('The human-readable name of this requirement type.'),
    ];

    $form['type']['id'] = [
      '#type' => 'machine_name',
      '#default_value' => '',
      '#machine_name' => [
        'exists' => [$this, 'requirementTypeExists'],
      ],
      '#description' => $this->t('A unique machine-readable name for this requirement type.'),
    ];

    $form['configuration'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Configuration'),
    ];

    $form['validation'] = [
      '#type' => 'details',
      '#title' => $this->t('Validation'),
      '#group' => 'configuration',
    ];

    $form['validation']['validation_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Validation Type'),
      '#options' => [
        'manual' => $this->t('Manual (Advisor Approval)'),
        'automatic' => $this->t('Automatic'),
        'hybrid' => $this->t('Hybrid (Auto + Manual)'),
      ],
      '#default_value' => 'manual',
    ];

    $form['fields'] = [
      '#type' => 'details',
      '#title' => $this->t('Fields'),
      '#group' => 'configuration',
    ];

    $form['fields']['field_table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Label'),
        $this->t('Machine Name'),
        $this->t('Type'),
        $this->t('Required'),
        $this->t('Operations'),
      ],
      '#empty' => $this->t('No fields defined.'),
    ];

    $form['fields']['add_field'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Field'),
      '#submit' => ['::addField'],
      '#ajax' => [
        'callback' => '::updateFieldTable',
        'wrapper' => 'field-table',
      ],
    ];

    $form['workflow'] = [
      '#type' => 'details',
      '#title' => $this->t('Workflow'),
      '#group' => 'configuration',
    ];

    $form['workflow']['states'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Workflow States'),
      '#description' => $this->t('Enter one state per line in the format: machine_name|Label'),
      '#default_value' => "pending|Pending\nin_progress|In Progress\ncompleted|Completed",
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Save Requirement Type'),
        '#button_type' => 'primary',
      ],
      'cancel' => [
        '#type' => 'link',
        '#title' => $this->t('Cancel'),
        '#url' => $this->getCancelUrl(),
        '#attributes' => ['class' => ['button']],
      ],
    ];

    return $form;
  }

  /**
   * Gets the cancel URL.
   *
   * @return \Drupal\Core\Url
   *   The URL to redirect to if cancelled.
   */
  public function getCancelUrl() {
    return new Url('dh_certificate.requirement_types');
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getValue('id');
    if ($this->requirementTypeExists($id)) {
      $form_state->setErrorByName('id', $this->t('The requirement type ID %id already exists.', [
        '%id' => $id,
      ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    
    // Create requirement type configuration
    $config = \Drupal::configFactory()->getEditable('dh_certificate.requirement_types');
    $config->set($values['id'], [
      'label' => $values['label'],
      'validation_type' => $values['validation_type'],
      'workflow_states' => $this->parseWorkflowStates($values['states']),
    ])->save();

    $this->messenger()->addStatus($this->t('Requirement type %label has been created.', [
      '%label' => $values['label'],
    ]));

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * Parse workflow states from textarea input.
   */
  protected function parseWorkflowStates($states_text) {
    $states = [];
    $lines = explode("\n", $states_text);
    foreach ($lines as $line) {
      $parts = explode('|', trim($line));
      if (count($parts) === 2) {
        $states[trim($parts[0])] = trim($parts[1]);
      }
    }
    return $states;
  }

  /**
   * Check if a requirement type already exists.
   */
  public function requirementTypeExists($id) {
    return \Drupal::configFactory()
      ->get('dh_certificate.requirement_types')
      ->get($id) !== NULL;
  }

}
