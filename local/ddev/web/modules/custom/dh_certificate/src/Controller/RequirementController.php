<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;

/**
 * Controller for requirements management.
 */
class RequirementController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new RequirementController.
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
   * Display the requirements overview page.
   */
  public function overview() {
    $build = [];
    
    try {
      // Load all requirement sets
      $storage = $this->entityTypeManager->getStorage('requirement_set');
      $query = $storage->getQuery()
        ->accessCheck(FALSE)
        ->sort('label');
      
      $ids = $query->execute();
      $sets = $storage->loadMultiple($ids);
      
      $data = [];
      foreach ($sets as $set_id => $set) {
        $data[$set_id] = [
          'id' => $set_id,
          'label' => $set->label(),
          'description' => $set->get('description'),
          'status' => $set->status(),
          'types' => [],
        ];

        // Get requirements directly using the getter method
        $requirements_data = $set->getRequirements();
        if (!empty($requirements_data)) {
          $requirement_ids = [];
          foreach ($requirements_data as $requirement_id) {
            if (!empty($requirement_id)) {
              $requirement_ids[] = $requirement_id;
            }
          }

          if (!empty($requirement_ids)) {
            $requirements = $this->entityTypeManager
              ->getStorage('requirement')
              ->loadMultiple($requirement_ids);

            foreach ($requirements as $requirement) {
              $type = $requirement->bundle();
              if (!isset($data[$set_id]['types'][$type])) {
                $data[$set_id]['types'][$type] = [];
              }
              $data[$set_id]['types'][$type][] = [
                'id' => $requirement->id(),
                'label' => $requirement->label(),
                'status' => $requirement->isEnabled(), // Changed from status() to isEnabled()
              ];
            }
          }
        }
      }

      // Add the overview
      $build['requirements'] = [
        '#theme' => 'dh_certificate_requirements_overview',
        '#sets' => $data,
        '#attached' => [
          'library' => ['dh_certificate/requirements-admin'],
        ],
        '#cache' => ['max-age' => 0],
      ];
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Error loading requirements: @error', [
        '@error' => $e->getMessage(),
      ]));
    }

    return $build;
  }

  /**
   * Page title callback for requirement set edit form.
   */
  public function editTitle($requirement_set) {
    return $this->t('Edit requirement set %label', ['%label' => $requirement_set->label()]);
  }

  /**
   * Returns the add requirement form for a set.
   */
  public function addRequirement($requirement_set) {
    $requirement = $this->entityTypeManager->getStorage('requirement')->create([
      'requirement_set' => $requirement_set->id(),
    ]);

    return $this->entityFormBuilder()->getForm($requirement, 'add');
  }

}
