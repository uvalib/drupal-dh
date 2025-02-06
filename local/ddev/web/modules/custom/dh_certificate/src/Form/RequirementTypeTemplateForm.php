<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the requirement type template forms.
 */
class RequirementTypeTemplateForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#description' => $this->t('Label for the requirement type template.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dh_certificate\Entity\RequirementTypeTemplate::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Requirement Type'),
      '#default_value' => $entity->getType(),
      '#options' => [
        'course' => $this->t('Course'),
        'milestone' => $this->t('Milestone'),
        'assessment' => $this->t('Assessment'),
      ],
      '#required' => TRUE,
    ];

    $form['weight'] = [
      '#type' => 'weight',
      '#title' => $this->t('Weight'),
      '#default_value' => $entity->getWeight(),
      '#description' => $this->t('Templates are ordered by weight, then label.'),
    ];

    $form['config'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Configuration'),
      '#default_value' => $entity->getConfig() ? json_encode($entity->getConfig(), JSON_PRETTY_PRINT) : '{}',
      '#description' => $this->t('Enter the configuration as JSON.'),
      '#rows' => 10,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Validate JSON configuration
    $config = $form_state->getValue('config');
    if (!empty($config)) {
      $decoded = json_decode($config, TRUE);
      if (json_last_error() !== JSON_ERROR_NONE) {
        $form_state->setErrorByName('config', $this->t('Configuration must be valid JSON.'));
      }
      else {
        $form_state->setValue('config', $decoded);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    $message = $status == SAVED_NEW
      ? $this->t('Created new requirement type template %label.')
      : $this->t('Updated requirement type template %label.');
    
    $this->messenger()->addStatus($message, ['%label' => $entity->label()]);
    $form_state->setRedirectUrl($entity->toUrl('collection'));
  }

}
