<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface;

class RequirementSetForm extends EntityForm {

  protected $requirementTypeManager;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.requirement_type_manager')
    );
  }

  public function __construct(RequirementTypeManagerInterface $requirement_type_manager) {
    $this->requirementTypeManager = $requirement_type_manager;
  }

  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $requirement_set = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $requirement_set->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $requirement_set->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dh_certificate\Entity\RequirementSet::load',
      ],
      '#disabled' => !$requirement_set->isNew(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $requirement_set->status(),
    ];

    $form['requirements'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Requirements'),
    ];

    foreach ($this->requirementTypeManager->getDefinitions() as $type_id => $definition) {
      $form["requirement_$type_id"] = [
        '#type' => 'details',
        '#title' => $definition['label'],
        '#group' => 'requirements',
        '#tree' => TRUE,
      ];

      $existing_requirements = $requirement_set->getRequirements()[$type_id] ?? [];

      $form["requirement_$type_id"]['enabled'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable @type requirements', ['@type' => $definition['label']]),
        '#default_value' => !empty($existing_requirements),
      ];

      $form["requirement_$type_id"]['config'] = [
        '#type' => 'container',
        '#states' => [
          'visible' => [
            ':input[name="requirement_' . $type_id . '[enabled]"]' => ['checked' => TRUE],
          ],
        ],
      ];

      // Add type-specific configuration fields
      switch ($type_id) {
        case 'course':
          $form["requirement_$type_id"]['config']['min_credits'] = [
            '#type' => 'number',
            '#title' => $this->t('Minimum credits required'),
            '#default_value' => $existing_requirements['min_credits'] ?? 0,
            '#min' => 0,
          ];
          break;

        case 'tool':
          $form["requirement_$type_id"]['config']['tools'] = [
            '#type' => 'checkboxes',
            '#title' => $this->t('Required tools'),
            '#options' => [
              'git' => 'Git',
              'python' => 'Python',
              'r' => 'R',
            ],
            '#default_value' => $existing_requirements['tools'] ?? [],
          ];
          break;
      }
    }

    return $form;
  }

  public function save(array $form, FormStateInterface $form_state) {
    $requirement_set = $this->entity;
    $status = $requirement_set->save();

    if ($status) {
      $this->messenger()->addStatus($this->t('Saved requirement set %label.', [
        '%label' => $requirement_set->label(),
      ]));
    }
    else {
      $this->messenger()->addError($this->t('Error saving requirement set %label.', [
        '%label' => $requirement_set->label(),
      ]));
    }

    $form_state->setRedirectUrl($requirement_set->toUrl('collection'));
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $requirement_set = $this->entity;

    // Process requirements
    $requirements = [];
    foreach ($this->requirementTypeManager->getDefinitions() as $type_id => $definition) {
      $values = $form_state->getValue("requirement_$type_id");
      if (!empty($values['enabled'])) {
        $requirements[$type_id] = $values['config'];
      }
    }

    $requirement_set->setRequirements($requirements);
  }
}
