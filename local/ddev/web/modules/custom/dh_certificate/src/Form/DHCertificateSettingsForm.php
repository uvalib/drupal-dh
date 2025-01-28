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

    $form['display_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Course Display Settings'),
    ];

    $form['display_settings']['default_view'] = [
      '#type' => 'select',
      '#title' => $this->t('Default View'),
      '#options' => [
        'grid' => $this->t('Grid'),
        'list' => $this->t('List'),
        'table' => $this->t('Table'),
      ],
      '#default_value' => $config->get('default_view') ?? 'grid',
    ];

    $form['display_settings']['items_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Items per page'),
      '#default_value' => $config->get('items_per_page') ?? 12,
      '#min' => 1,
      '#max' => 100,
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
      ->set('default_view', $form_state->getValue('default_view'))
      ->set('items_per_page', $form_state->getValue('items_per_page'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
