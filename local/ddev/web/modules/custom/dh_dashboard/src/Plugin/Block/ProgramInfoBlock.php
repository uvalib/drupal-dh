<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a program info block.
 *
 * @Block(
 *   id = "program_info_block",
 *   admin_label = @Translation("Program Information"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class ProgramInfoBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new ProgramInfoBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    
    try {
      $node_storage = $this->entityTypeManager->getStorage('node');
      $query = $node_storage->getQuery()
        ->condition('type', 'program_info')
        ->condition('status', 1)
        ->sort('created', 'DESC')
        ->accessCheck(TRUE);
      $nids = $query->execute();
      
      if (!empty($nids)) {
        $nodes = $node_storage->loadMultiple($nids);
        foreach ($nodes as $node) {
          $build['programs'][] = [
            '#type' => 'container',
            '#attributes' => ['class' => ['program-item']],
            'title' => [
              '#type' => 'markup',
              '#markup' => '<h3>' . $node->getTitle() . '</h3>',
            ],
            'description' => [
              '#type' => 'markup',
              '#markup' => $node->field_description->value,
            ],
          ];
        }
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_dashboard')->error('Error loading program info: @message', ['@message' => $e->getMessage()]);
    }

    $build['#attached']['library'][] = 'dh_dashboard/program_info';
    
    return $build;
  }

}
