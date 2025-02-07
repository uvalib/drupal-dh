<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for taxonomy structure monitoring.
 */
class TaxonomyMonitorController extends ControllerBase {

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
   * Constructs a TaxonomyMonitorController.
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
   * Displays the taxonomy structure overview.
   *
   * @return array
   *   A render array representing the taxonomy structure overview page.
   */
  public function overview() {
    $structure_data = $this->state->get('dh_certificate.monitor.taxonomy.structure', []);
    $last_check = $this->state->get('dh_certificate.monitor.taxonomy.last_check', 0);

    return [
      '#theme' => 'dh_certificate_structure_data',
      '#entity_type' => 'taxonomy',
      '#structure_data' => $structure_data,
      '#last_updated' => $last_check,
      '#attached' => [
        'library' => ['dh_certificate/structure-monitor'],
      ],
      '#cache' => [
        'tags' => ['dh_certificate:monitor:taxonomy'],
        'max-age' => 300,
      ],
    ];
  }

  /**
   * Records the current taxonomy structure.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect to the taxonomy structure overview page.
   */
  public function recordStructure() {
    try {
      $vocab_storage = $this->entityTypeManager->getStorage('taxonomy_vocabulary');
      $vocabularies = $vocab_storage->loadMultiple();
      $structure_data = [];

      foreach ($vocabularies as $vocabulary) {
        $structure_data[$vocabulary->id()] = [
          'label' => $vocabulary->label(),
          'fields' => [],
          'terms' => [],
        ];

        // Get field definitions
        $fields = \Drupal::service('entity_field.manager')
          ->getFieldDefinitions('taxonomy_term', $vocabulary->id());

        foreach ($fields as $field_name => $field) {
          $structure_data[$vocabulary->id()]['fields'][$field_name] = [
            'label' => $field->getLabel(),
            'type' => $field->getType(),
            'required' => $field->isRequired(),
          ];
        }

        // Get term count
        $term_count = $this->entityTypeManager->getStorage('taxonomy_term')
          ->getQuery()
          ->accessCheck(FALSE)
          ->condition('vid', $vocabulary->id())
          ->count()
          ->execute();

        $structure_data[$vocabulary->id()]['terms']['count'] = $term_count;
      }

      $this->state->set('dh_certificate.monitor.taxonomy.structure', $structure_data);
      $this->state->set('dh_certificate.monitor.taxonomy.last_check', \Drupal::time()->getRequestTime());

      $this->messenger()->addStatus($this->t('Taxonomy structure has been recorded.'));
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Failed to record taxonomy structure: @error', [
        '@error' => $e->getMessage(),
      ]));
    }

    return $this->redirect('dh_certificate.taxonomy_monitor');
  }
}
