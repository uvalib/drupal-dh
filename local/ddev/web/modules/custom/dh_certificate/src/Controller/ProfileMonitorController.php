<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for profile structure monitoring.
 */
class ProfileMonitorController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a ProfileMonitorController.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, StateInterface $state) {
    $this->entityTypeManager = $entity_type_manager;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('state')
    );
  }

  /**
   * Displays the profile structure overview.
   *
   * @return array
   *   A render array representing the profile structure overview page.
   */
  public function overview() {
    $structure_data = $this->state->get('dh_certificate.monitor.profile.structure', []);
    $last_check = $this->state->get('dh_certificate.monitor.profile.last_check', 0);

    return [
      '#theme' => 'dh_certificate_structure_data',
      '#entity_type' => 'profile',
      '#structure_data' => $structure_data,
      '#last_updated' => $last_check,
      '#attached' => [
        'library' => ['dh_certificate/structure-monitor'],
      ],
      '#cache' => [
        'tags' => ['dh_certificate:monitor:profile'],
        'max-age' => 300,
      ],
    ];
  }

  /**
   * Records the current profile structure.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect to the profile structure overview page.
   */
  public function recordStructure() {
    try {
      $profile_types = $this->entityTypeManager->getStorage('profile_type')->loadMultiple();
      $structure_data = [];

      foreach ($profile_types as $profile_type) {
        $structure_data[$profile_type->id()] = [
          'label' => $profile_type->label(),
          'fields' => [],
        ];

        // Get field definitions for this profile type
        $fields = \Drupal::service('entity_field.manager')
          ->getFieldDefinitions('profile', $profile_type->id());

        foreach ($fields as $field_name => $field) {
          $structure_data[$profile_type->id()]['fields'][$field_name] = [
            'label' => $field->getLabel(),
            'type' => $field->getType(),
            'required' => $field->isRequired(),
          ];
        }
      }

      $this->state->set('dh_certificate.monitor.profile.structure', $structure_data);
      $this->state->set('dh_certificate.monitor.profile.last_check', \Drupal::time()->getRequestTime());

      $this->messenger()->addStatus($this->t('Profile structure has been recorded.'));
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Failed to record profile structure: @error', [
        '@error' => $e->getMessage(),
      ]));
    }

    return $this->redirect('dh_certificate.profile_monitor');
  }
}
