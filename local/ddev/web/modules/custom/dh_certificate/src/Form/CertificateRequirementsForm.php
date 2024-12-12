<?php
// modules/custom/dh_certificate/src/Form/CertificateRequirementsForm.php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CertificateRequirementsForm extends FormBase
{
    protected $entityTypeManager;

    public function __construct(EntityTypeManagerInterface $entity_type_manager)
    {
        $this->entityTypeManager = $entity_type_manager;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager')
        );
    }

    public function getFormId()
    {
        return 'dh_certificate_requirements_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $user = $this->entityTypeManager->getStorage('user')
        ->load($this->currentUser()->id());
    
        $requirements = $user->get('field_dh_requirements')->referencedEntities();

        $form['requirements'] = [
        '#type' => 'container',
        '#tree' => true,
        ];

        foreach ($requirements as $delta => $requirement) {
            $form['requirements'][$delta] = [
            '#type' => 'container',
            '#attributes' => [
            'class' => ['requirement-item'],
            ],
            ];

            if ($requirement->bundle() === 'course_requirement') {
                $form['requirements'][$delta]['info'] = [
                '#markup' => $this->t('@course (@number)', [
                '@course' => $requirement->get('field_course_name')->value,
                '@number' => $requirement->get('field_course_number')->value,
                ]),
                ];
            } else {
                $form['requirements'][$delta]['info'] = [
                '#markup' => $requirement->get('field_requirement_name')->value,
                ];
            }

            $form['requirements'][$delta]['completed'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Completed'),
            '#default_value' => $requirement->get('field_completed')->value,
            ];
        }

        $form['actions'] = [
        '#type' => 'actions',
        'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Save Progress'),
        ],
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $user = $this->entityTypeManager->getStorage('user')
        ->load($this->currentUser()->id());
    
        $requirements = $user->get('field_dh_requirements')->referencedEntities();
        $values = $form_state->getValue('requirements');

        foreach ($requirements as $delta => $requirement) {
            if (isset($values[$delta]['completed'])) {
                $requirement->set('field_completed', $values[$delta]['completed']);
                $requirement->save();
            }
        }

        $this->messenger()->addMessage($this->t('Progress updated successfully.'));
    }
}
