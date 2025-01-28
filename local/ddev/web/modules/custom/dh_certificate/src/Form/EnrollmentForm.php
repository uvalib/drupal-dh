<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Form controller for course enrollment edit forms.
 */
class EnrollmentForm extends FormBase {

  /**
   * The logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new EnrollmentForm.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    LoggerChannelFactoryInterface $logger_factory,
    Connection $database,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->logger = $logger_factory->get('dh_certificate');
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
    // Remove debug logging
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory'),
      $container->get('database'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dh_certificate_enrollment_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getUserOptions() {
    $query = $this->entityTypeManager->getStorage('user')->getQuery()
      ->condition('status', 1)
      ->condition('uid', 0, '>')  // Exclude anonymous user
      ->sort('name')
      ->accessCheck(FALSE);  // Explicitly disable access checking for admin operation
    
    $uids = $query->execute();
    $users = $this->entityTypeManager->getStorage('user')->loadMultiple($uids);
    
    $options = [];
    foreach ($users as $user) {
      $options[$user->id()] = sprintf(
        '%s (%s)',
        $user->getDisplayName(),
        $user->getEmail() ?: $user->getAccountName()
      );
    }
    
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  protected function getCourseOptions() {
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->sort('field_course_mnemonic')
      ->sort('title')
      ->accessCheck(FALSE);
    
    $nids = $query->execute();
    $courses = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    
    $options = [];
    foreach ($courses as $course) {
      $mnemonic = $course->hasField('field_course_mnemonic') ? 
        $course->get('field_course_mnemonic')->value : '';
      $options[$course->id()] = $mnemonic ? 
        sprintf('%s: %s', $mnemonic, $course->label()) : 
        $course->label();
    }
    
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    // Add libraries
    $form['#attached']['library'][] = 'core/drupal.form';
    $form['#attached']['library'][] = 'core/drupal.states';

    $enrollment = NULL;

    if ($id) {
      $enrollment = $this->database->select('course_enrollment', 'ce')
        ->fields('ce')
        ->condition('id', $id)
        ->execute()
        ->fetchObject();

      if (!$enrollment) {
        $this->messenger()->addError($this->t('Enrollment not found.'));
        return $form;
      }
    }

    // Add the enrollment ID as a hidden field if we're editing
    if ($enrollment) {
      $form['id'] = [
        '#type' => 'hidden',
        '#value' => $enrollment->id,
      ];
    }

    // Replace user autocomplete with select list
    $form['uid'] = [
      '#type' => 'select',
      '#title' => $this->t('User'),
      '#options' => $this->getUserOptions(),
      '#required' => TRUE,
      '#default_value' => $enrollment ? $enrollment->uid : NULL,
      '#empty_option' => $this->t('- Select a user -'),
      // Add wrapper for potential AJAX updates
      '#prefix' => '<div id="user-wrapper">',
      '#suffix' => '</div>',
    ];

    // Replace course autocomplete with select
    $form['course_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Course'),
      '#options' => $this->getCourseOptions(),
      '#required' => TRUE,
      '#default_value' => $enrollment ? $enrollment->course_id : NULL,
      '#empty_option' => $this->t('- Select a course -'),
    ];

    // Status selection
    $form['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Status'),
      '#options' => [
        'pending' => $this->t('Pending'),
        'in-progress' => $this->t('In Progress'),
        'completed' => $this->t('Completed'),
      ],
      '#required' => TRUE,
      '#default_value' => $enrollment ? $enrollment->status : 'pending',
    ];

    // Update completed date field with improved states configuration
    $form['completed_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Completion Date'),
      '#default_value' => $enrollment && $enrollment->completed_date ? 
        DrupalDateTime::createFromTimestamp($enrollment->completed_date) : NULL,
      '#states' => [
        'visible' => [
          'select[name="status"]' => ['value' => 'completed'],
        ],
        'required' => [
          'select[name="status"]' => ['value' => 'completed'],
        ],
        'invisible' => [
          'select[name="status"]' => [
            ['value' => 'pending'],
            ['value' => 'in-progress'],
          ],
        ],
      ],
      '#prefix' => '<div id="completion-date-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $enrollment ? $this->t('Update Enrollment') : $this->t('Create Enrollment'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('status') === 'completed' && empty($form_state->getValue('completed_date'))) {
      $form_state->setErrorByName('completed_date', $this->t('Completion date is required when status is completed.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Prepare the record
    $record = [
      'uid' => $values['uid'],
      'course_id' => $values['course_id'],
      'status' => $values['status'],
      'completed_date' => NULL,  // Default to NULL
    ];

    // Only set completion date if status is completed and date is provided
    if ($values['status'] === 'completed' && !empty($values['completed_date'])) {
      $record['completed_date'] = $values['completed_date']->getTimestamp();
    }

    try {
      if (!empty($values['id'])) {
        // Update existing enrollment
        $this->database->update('course_enrollment')
          ->fields($record)
          ->condition('id', $values['id'])
          ->execute();
        $this->messenger()->addMessage($this->t('Enrollment updated successfully.'));
      }
      else {
        // Insert new enrollment
        $this->database->insert('course_enrollment')
          ->fields($record)
          ->execute();
        $this->messenger()->addMessage($this->t('Enrollment created successfully.'));
      }

      // Redirect to the enrollment list
      $form_state->setRedirect('dh_certificate.enrollment_list');
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('An error occurred while saving the enrollment.'));
      $this->logger('dh_certificate')->error('Error saving enrollment: @message', ['@message' => $e->getMessage()]);
    }
  }

}
