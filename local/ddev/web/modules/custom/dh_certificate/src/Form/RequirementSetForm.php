<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface;

class RequirementSetForm extends EntityForm {

  protected $requirementTypeManager;

  public function __construct(RequirementTypeManagerInterface $requirement_type_manager) {
    $this->requirementTypeManager = $requirement_type_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.requirement_type_manager')
    );
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

    // Add requirements fieldset
    $form['requirements'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Requirements'),
    ];

    // Get available requirement types from plugin manager
    $requirement_types = $this->requirementTypeManager->getDefinitions();
    $existing_requirements = $requirement_set->getRequirements();

    foreach ($requirement_types as $type_id => $definition) {
      $form["requirement_$type_id"] = [
        '#type' => 'details',
        '#title' => $definition['label'],
        '#group' => 'requirements',
        '#tree' => TRUE,
      ];

      $existing_config = $existing_requirements[$type_id] ?? [];

      $form["requirement_$type_id"]['enabled'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable @type requirements', ['@type' => $definition['label']]),
        '#default_value' => !empty($existing_config),
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
      $config_form = &$form["requirement_$type_id"]['config'];
      
      switch ($type_id) {
        case 'course':
          $this->buildCourseRequirementForm($config_form, $existing_config);
          break;
        
        case 'tool':
          $this->buildToolRequirementForm($config_form, $existing_config);
          break;

        case 'project':
          $this->buildProjectRequirementForm($config_form, $existing_config);
          break;
      }
    }

    return $form;
  }

  protected function buildCourseRequirementForm(array &$form, array $existing_config) {
    $form['minimum_credits'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum credits required'),
      '#default_value' => $existing_config['minimum_credits'] ?? 0,
      '#min' => 0,
      '#required' => TRUE,
    ];

    $form['course_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Allowed course types'),
      '#options' => [
        'core' => $this->t('Core'),
        'methods' => $this->t('Methods'),
        'elective' => $this->t('Elective'),
      ],
      '#default_value' => $existing_config['course_types'] ?? ['core'],
      '#required' => TRUE,
    ];
  }

  protected function buildToolRequirementForm(array &$form, array $existing_config) {
    $form['tools'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Required tools'),
      '#options' => [
        'git' => 'Git',
        'python' => 'Python',
        'r' => 'R',
        'javascript' => 'JavaScript',
      ],
      '#default_value' => $existing_config['tools'] ?? [],
    ];

    $form['minimum_proficiency'] = [
      '#type' => 'select',
      '#title' => $this->t('Minimum proficiency level'),
      '#options' => [
        1 => $this->t('Basic'),
        2 => $this->t('Intermediate'),
        3 => $this->t('Advanced'),
      ],
      '#default_value' => $existing_config['minimum_proficiency'] ?? 1,
    ];
  }

  protected function buildProjectRequirementForm(array &$form, array $existing_config) {
    $form['milestones'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Milestone'),
        $this->t('Deadline'),
        $this->t('Operations'),
      ],
      '#empty' => $this->t('No milestones defined.'),
    ];

    $milestones = $existing_config['milestones'] ?? [];
    foreach ($milestones as $id => $milestone) {
      $form['milestones'][$id]['label'] = [
        '#type' => 'textfield',
        '#default_value' => $milestone['label'],
        '#required' => TRUE,
      ];
      
      $form['milestones'][$id]['deadline'] = [
        '#type' => 'textfield',
        '#default_value' => $milestone['deadline'],
        '#description' => $this->t('e.g. "+2 months"'),
      ];
    }

    $form['add_milestone'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add milestone'),
      '#submit' => ['::addMilestone'],
      '#ajax' => [
        'callback' => '::updateMilestoneTable',
        'wrapper' => 'milestones-table',
      ],
    ];
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
}
