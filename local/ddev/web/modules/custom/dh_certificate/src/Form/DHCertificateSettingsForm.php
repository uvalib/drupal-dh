<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure DH Certificate settings.
 */
class DHCertificateSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dh_certificate.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dh_certificate_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dh_certificate.settings');

    $form['show_debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show debug information'),
      '#description' => $this->t('Enable to show additional debugging information in certificate blocks.'),
      '#default_value' => $config->get('show_debug'),
    ];

    $form['requirements'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Certificate Requirements'),
    ];

    $form['requirements']['core_credits'] = [
      '#type' => 'number',
      '#title' => $this->t('Required Core Credits'),
      '#default_value' => $config->get('core_credits') ?? 12,
      '#min' => 0,
      '#required' => TRUE,
    ];

    $form['requirements']['elective_credits'] = [
      '#type' => 'number',
      '#title' => $this->t('Required Elective Credits'),
      '#default_value' => $config->get('elective_credits') ?? 6,
      '#min' => 0,
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dh_certificate.settings')
      ->set('show_debug', $form_state->getValue('show_debug'))
      ->set('core_credits', $form_state->getValue('core_credits'))
      ->set('elective_credits', $form_state->getValue('elective_credits'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
