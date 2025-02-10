<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DHCertificateRequirementsForm extends ConfigFormBase {

  protected $entityTypeManager;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

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

    // Load all active requirements
    $requirements = $this->entityTypeManager->getStorage('requirement')
      ->loadByProperties(['status' => TRUE]);

    // Group requirements by type
    $grouped_requirements = [];
    foreach ($requirements as $requirement) {
      $grouped_requirements[$requirement->getType()][] = $requirement;
    }

    // Build form sections for each requirement type
    $requirement_types = ['course' => 'Courses', 'skill' => 'Skills', 'project' => 'Projects'];
    foreach ($requirement_types as $type => $label) {
      $form[$type] = [
        '#type' => 'details',
        '#title' => $this->t('@label Requirements', ['@label' => $label]),
        '#group' => 'requirements',
        '#tree' => TRUE,
      ];

      $form[$type]['enabled'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('Enable @label', ['@label' => $label]),
        '#options' => $this->getRequirementOptions($grouped_requirements[$type] ?? []),
        '#default_value' => $config->get("{$type}_requirements") ?: [],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  protected function getRequirementOptions(array $requirements) {
    $options = [];
    foreach ($requirements as $requirement) {
      $options[$requirement->id()] = $requirement->label();
    }
    return $options;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('dh_certificate.requirements');
    
    $requirement_types = ['course', 'skill', 'project'];
    foreach ($requirement_types as $type) {
      $config->set("{$type}_requirements", array_filter($form_state->getValue([$type, 'enabled'])));
    }
    
    $config->save();
    parent::submitForm($form, $form_state);
  }
}
