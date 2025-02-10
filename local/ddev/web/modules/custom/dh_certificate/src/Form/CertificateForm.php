<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form handler for the Certificate add and edit forms.
 */
class CertificateForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $certificate = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $certificate->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $certificate->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dh_certificate\Entity\Certificate::load',
      ],
      '#disabled' => !$certificate->isNew(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Active'),
      '#default_value' => $certificate->status(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);
    $certificate = $this->entity;

    $message_args = ['%label' => $certificate->label()];
    $message = $result == SAVED_NEW 
      ? $this->t('Created new certificate %label.', $message_args)
      : $this->t('Updated certificate %label.', $message_args);
    $this->messenger()->addStatus($message);

    $form_state->setRedirectUrl($certificate->toUrl('collection'));
    return $result;
  }

}
