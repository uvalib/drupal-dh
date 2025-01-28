<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class DHCertificateRequirementsForm extends ConfigFormBase {
  
  protected function getEditableConfigNames() {
    return ['dh_certificate.requirements'];
  }

  public function getFormId() {
    return 'dh_certificate_requirements_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dh_certificate.requirements');

    $form['requirements'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Certificate Requirements'),
    ];

    $form['core_courses'] = [
      '#type' => 'details',
      '#title' => $this->t('Core Courses'),
      '#group' => 'requirements',
      '#tree' => TRUE,
    ];

    $form['electives'] = [
      '#type' => 'details',
      '#title' => $this->t('Electives'),
      '#group' => 'requirements',
      '#tree' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }
}
