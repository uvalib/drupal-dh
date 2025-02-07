<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Controller for requirement pages.
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
   * Displays the requirements overview page.
   */
  public function overview() {
    // Get requirement sets
    $requirement_sets = $this->entityTypeManager->getStorage('requirement_set')
      ->loadMultiple();
    
    // Get requirement types
    $requirement_types = $this->entityTypeManager->getStorage('requirement_type')
      ->loadMultiple();

    // Get requirement templates
    $requirement_templates = $this->entityTypeManager->getStorage('requirement_type_template')
      ->loadMultiple();

    $build = [
      '#theme' => 'dh_certificate_requirements',
      '#title' => $this->t('Requirements Overview'),
      '#content' => [
        'summary' => [
          '#type' => 'details',
          '#title' => $this->t('Summary'),
          '#open' => TRUE,
          'content' => [
            '#theme' => 'item_list',
            '#items' => [
              $this->t('Total Requirement Sets: @count', ['@count' => count($requirement_sets)]),
              $this->t('Total Requirement Types: @count', ['@count' => count($requirement_types)]),
              $this->t('Total Templates: @count', ['@count' => count($requirement_templates)]),
            ],
          ],
        ],
        'sets' => [
          '#type' => 'details',
          '#title' => $this->t('Requirement Sets'),
          '#open' => TRUE,
          'content' => [
            '#theme' => 'table',
            '#header' => ['Name', 'Status', 'Requirements', 'Operations'],
            '#rows' => $this->buildSetRows($requirement_sets),
            '#empty' => $this->t('No requirement sets found.'),
          ],
        ],
        'types' => [
          '#type' => 'details',
          '#title' => $this->t('Recent Requirement Types'),
          '#open' => TRUE,
          'content' => [
            '#theme' => 'table',
            '#header' => ['Name', 'Template', 'Used In', 'Operations'],
            '#rows' => $this->buildTypeRows($requirement_types),
            '#empty' => $this->t('No requirement types found.'),
          ],
        ],
      ],
      '#cache' => [
        'tags' => [
          'requirement_set_list',
          'requirement_type_list',
          'requirement_type_template_list',
        ],
      ],
    ];

    return $build;
  }

  protected function buildSetRows($sets) {
    $rows = [];
    foreach ($sets as $set) {
      $rows[] = [
        $set->label(),
        $set->status() ? $this->t('Enabled') : $this->t('Disabled'),
        $this->countSetRequirements($set),
        [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'edit' => [
                'title' => $this->t('Edit'),
                'url' => $set->toUrl('edit-form'),
              ],
            ],
          ],
        ],
      ];
    }
    return $rows;
  }

  protected function buildTypeRows($types) {
    $rows = [];
    foreach ($types as $type) {
      $template = $type->getTemplate() ? $type->getTemplate()->label() : $this->t('Custom');
      $rows[] = [
        $type->label(),
        $template,
        $this->countTypeUsage($type),
        [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'edit' => [
                'title' => $this->t('Edit'),
                'url' => $type->toUrl('edit-form'),
              ],
            ],
          ],
        ],
      ];
    }
    return $rows;
  }

  protected function countSetRequirements($set) {
    // Add implementation to count requirements in a set
    return '0'; // Placeholder - implement actual counting logic
  }

  protected function countTypeUsage($type) {
    // Add implementation to count where this type is used
    return '0'; // Placeholder - implement actual counting logic
  }
}
