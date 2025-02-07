<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for requirement type management.
 */
class RequirementTypeController extends ControllerBase {

  /**
   * The requirement type manager.
   *
   * @var \Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface
   */
  protected $requirementTypeManager;

  /**
   * Constructs a RequirementTypeController.
   *
   * @param \Drupal\dh_certificate\RequirementType\RequirementTypeManagerInterface $requirement_type_manager
   *   The requirement type manager.
   */
  public function __construct(RequirementTypeManagerInterface $requirement_type_manager) {
    $this->requirementTypeManager = $requirement_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dh_certificate.requirement_type_manager')
    );
  }

  /**
   * Displays an overview of requirement types.
   *
   * @return array
   *   A render array representing the requirement types overview page.
   */
  public function overview() {
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['requirement-type-overview']],
    ];

    // Add action links
    $build['actions'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['action-links']],
      'add' => [
        '#type' => 'link',
        '#title' => $this->t('Add requirement type'),
        '#url' => Url::fromRoute('entity.requirement_type.add_form'),
        '#attributes' => ['class' => ['button', 'button--action', 'button--primary']],
      ],
    ];

    // Build the table
    $header = [
      'label' => $this->t('Label'),
      'id' => $this->t('Machine name'),
      'description' => $this->t('Description'),
      'operations' => $this->t('Operations'),
    ];

    $rows = [];
    $definitions = $this->requirementTypeManager->getDefinitions();

    foreach ($definitions as $id => $definition) {
      $row = [];
      $row['label'] = $definition['label'];
      $row['id'] = $id;
      $row['description'] = $definition['description'] ?? '';
      
      // Build operations
      $operations = [];
      $operations['edit'] = [
        'title' => $this->t('Edit'),
        'url' => Url::fromRoute('entity.requirement_type.edit_form', ['requirement_type' => $id]),
      ];
      
      $row['operations'] = [
        'data' => [
          '#type' => 'operations',
          '#links' => $operations,
        ],
      ];

      $rows[] = $row;
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No requirement types found.'),
      '#attributes' => [
        'class' => ['requirement-type-list'],
      ],
    ];

    return $build;
  }

  /**
   * Returns the page title for requirement type pages.
   *
   * @param string $requirement_type
   *   The requirement type ID.
   *
   * @return string
   *   The page title.
   */
  public function getTitle($requirement_type = NULL) {
    if ($requirement_type && $this->requirementTypeManager->hasDefinition($requirement_type)) {
      $definition = $this->requirementTypeManager->getDefinition($requirement_type);
      return $this->t('Edit @label requirement type', ['@label' => $definition['label']]);
    }
    return $this->t('Add requirement type');
  }

  /**
   * Title callback for the edit form.
   */
  public function editTitle($requirement_type) {
    if ($requirement_type && $this->requirementTypeManager->hasDefinition($requirement_type)) {
      $definition = $this->requirementTypeManager->getDefinition($requirement_type);
      return $this->t('Edit @label requirement type', ['@label' => $definition['label']]);
    }
    return $this->t('Add requirement type');
  }

}
