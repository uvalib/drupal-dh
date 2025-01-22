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
    return 'dh_certificate_settings';
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

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dh_certificate.settings')
      ->set('show_debug', $form_state->getValue('show_debug'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
