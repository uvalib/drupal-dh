<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Controller for certificate requirements management.
 */
class RequirementsController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new RequirementsController.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Displays the requirements overview page.
   */
  public function overview() {
    $requirement_sets = $this->entityTypeManager
      ->getStorage('requirement_set')
      ->loadMultiple();

    $build = [
      '#theme' => 'dh_certificate_requirements_overview',
      '#requirement_sets' => [],
      '#attached' => [
        'library' => ['dh_certificate/requirements-admin'],
      ],
    ];

    foreach ($requirement_sets as $set) {
      $requirements = $this->loadRequirementsForSet($set->id());
      
      $build['#requirement_sets'][] = [
        'id' => $set->id(),
        'label' => $set->label(),
        'description' => $set->getDescription(),
        'requirements' => $requirements,
        'add_requirement_url' => Url::fromRoute('dh_certificate.requirement.add', ['requirement_set' => $set->id()])->toString(),
        'edit_set_url' => Url::fromRoute('entity.requirement_set.edit_form', ['requirement_set' => $set->id()])->toString(),
      ];
    }

    // Add action buttons
    $build['actions'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['requirements-actions']],
      'add_set' => [
        '#type' => 'link',
        '#title' => $this->t('Add Requirement Set'),
        '#url' => Url::fromRoute('entity.requirement_set.add_form'),
        '#attributes' => ['class' => ['button', 'button--primary']],
      ],
    ];

    return $build;
  }

  /**
   * Loads requirements for a specific set.
   */
  protected function loadRequirementsForSet($set_id) {
    $requirements = [];
    $requirement_storage = $this->entityTypeManager->getStorage('requirement');
    
    try {
      $ids = $requirement_storage->getQuery()
        ->condition('requirement_set', $set_id)
        ->sort('weight')
        ->accessCheck(TRUE)
        ->execute();

      if (!empty($ids)) {
        $entities = $requirement_storage->loadMultiple($ids);
        foreach ($entities as $requirement) {
          if ($requirement->access('view')) {
            $requirements[] = [
              'id' => $requirement->id(),
              'label' => $requirement->label(),
              'type' => $requirement->getType(),
              'edit_url' => $requirement->toUrl('edit-form')->toString(),
              'delete_url' => $requirement->toUrl('delete-form')->toString(),
            ];
          }
        }
      }
    }
    catch (\Exception $e) {
      $this->logger('dh_certificate')->error('Error loading requirements: @message', [
        '@message' => $e->getMessage(),
      ]);
    }

    return $requirements;
  }
}
