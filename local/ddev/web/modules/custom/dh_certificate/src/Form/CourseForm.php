<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for adding/editing certificate courses.
 */
class CourseForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dh_certificate_course_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Course Title'),
      '#required' => TRUE,
    ];

    $form['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Course Code'),
      '#required' => TRUE,
    ];

    $form['credits'] = [
      '#type' => 'number',
      '#title' => $this->t('Credits'),
      '#required' => TRUE,
      '#min' => 0,
      '#step' => 0.5,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Course'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    
    // Create a new course node
    $node = \Drupal::entityTypeManager()->getStorage('node')->create([
      'type' => 'course',
      'title' => $values['title'],
      'field_course_code' => $values['code'],
      'field_credits' => $values['credits'],
    ]);
    
    $node->save();
    
    $this->messenger()->addStatus($this->t('Course %title has been created.', ['%title' => $values['title']]));
    $form_state->setRedirect('dh_certificate.courses');
  }
}
