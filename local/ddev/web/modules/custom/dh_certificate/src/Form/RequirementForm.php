<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form handler for the Requirement entity edit forms.
 */
class RequirementForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $requirement = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $requirement->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $requirement->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dh_certificate\Entity\Requirement::load',
      ],
      '#disabled' => !$requirement->isNew(),
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#default_value' => $requirement->getType(),
      '#options' => [
        'course' => $this->t('Course'),
        'skill' => $this->t('Skill'),
        'project' => $this->t('Project'),
      ],
      '#required' => TRUE,
    ];

    $form['settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Settings'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $settings = $requirement->getSettings();
    
    $form['settings']['required_credits'] = [
      '#type' => 'number',
      '#title' => $this->t('Required Credits'),
      '#default_value' => $settings['required_credits'] ?? 3,
      '#min' => 0,
      '#step' => 1,
    ];

    $form['settings']['minimum_grade'] = [
      '#type' => 'select',
      '#title' => $this->t('Minimum Grade'),
      '#default_value' => $settings['minimum_grade'] ?? 'C',
      '#options' => [
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
        'D' => 'D',
      ],
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $requirement->isEnabled(),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    $form['actions']['cancel'] = [
      '#markup' => '<a href="' . Url::fromRoute('entity.requirement.collection')->toString() . '" class="button">' . $this->t('Cancel') . '</a>',
    ];

    // Simple HTML debug output
    $form['debug'] = [
      '#markup' => '<!-- Debug: Actions Array: ' . print_r($form['actions'], TRUE) . ' -->',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $requirement = $this->entity;
    $status = $requirement->save();

    $args = ['%label' => $requirement->label()];
    if ($status) {
      $this->messenger()->addStatus($this->t('Saved the %label requirement.', $args));
    }
    else {
      $this->messenger()->addError($this->t('The %label requirement was not saved.', $args));
    }

    $form_state->setRedirect('entity.requirement.collection');
  }

}
