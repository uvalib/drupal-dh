<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Provides a confirmation form for deleting an enrollment.
 */
class DeleteEnrollmentConfirmForm extends ConfirmFormBase {

  protected $database;
  protected $entityTypeManager;
  protected $logger;
  protected $id;
  protected $enrollment;
  protected $courseData;
  protected $userData;

  public function __construct(
    Connection $database, 
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger_factory->get('dh_certificate');
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('entity_type.manager'),
      $container->get('logger.factory')
    );
  }

  public function getFormId() {
    return 'dh_certificate_delete_enrollment_confirm_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    try {
      $this->id = $id;
      
      // Load enrollment data with error checking
      $query = $this->database->select('course_enrollment', 'ce')
        ->fields('ce')
        ->condition('id', $this->id);
      $this->enrollment = $query->execute()->fetchObject();

      if (!$this->enrollment) {
        $this->messenger()->addError($this->t('Enrollment not found.'));
        return $this->redirect('dh_certificate.enrollment_list');
      }

      // Load course and user data
      $this->courseData = $this->entityTypeManager->getStorage('node')->load($this->enrollment->course_id);
      $this->userData = $this->entityTypeManager->getStorage('user')->load($this->enrollment->uid);

      if (!$this->courseData || !$this->userData) {
        throw new \Exception('Required course or user data missing');
      }

      $form = parent::buildForm($form, $form_state);

      // Add enrollment details as a table
      $row = [
        $this->userData->getDisplayName() . ' (' . $this->userData->getEmail() . ')',
        $this->courseData->label(),
        ucfirst($this->enrollment->status),
      ];

      // Only add dates if they exist
      if (!empty($this->enrollment->created)) {
        $row[] = \Drupal::service('date.formatter')->format($this->enrollment->created);
      } else {
        $row[] = $this->t('N/A');
      }

      if ($this->enrollment->status === 'completed' && !empty($this->enrollment->completed_date)) {
        $row[] = \Drupal::service('date.formatter')->format($this->enrollment->completed_date);
      } else {
        $row[] = $this->t('N/A');
      }

      $form['details'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Student'),
          $this->t('Course'),
          $this->t('Status'),
          $this->t('Enrolled'),
          $this->t('Completed'),
        ],
        '#rows' => [$row],
        '#weight' => -10,
      ];

      return $form;

    } catch (\Exception $e) {
      $this->logger->error('Error building delete form: @error', ['@error' => $e->getMessage()]);
      $this->messenger()->addError($this->t('Unable to load enrollment details.'));
      return $this->redirect('dh_certificate.enrollment_list');
    }
  }

  public function getQuestion() {
    return $this->t('Are you sure you want to delete this enrollment?');
  }

  public function getCancelUrl() {
    return new Url('dh_certificate.enrollment_list');
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($this->id) {
      $this->database->delete('course_enrollment')
        ->condition('id', $this->id)
        ->execute();
      $this->messenger()->addStatus($this->t('The enrollment has been deleted.'));
    }
    $form_state->setRedirectUrl($this->getCancelUrl());
  }
}
