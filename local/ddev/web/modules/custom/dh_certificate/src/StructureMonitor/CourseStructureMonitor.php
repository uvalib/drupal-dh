<?php

namespace Drupal\dh_certificate\StructureMonitor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay;

/**
 * Monitors changes in course structure.
 */
class CourseStructureMonitor extends EntityStructureMonitorBase {

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new CourseStructureMonitor.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory,
    StateInterface $state,
    EntityFieldManagerInterface $entity_field_manager,
    ConfigFactoryInterface $config_factory
  ) {
    parent::__construct($entity_type_manager, $logger_factory, $state);
    $this->entityFieldManager = $entity_field_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function getMonitorId() {
    return 'course_structure';
  }

  /**
   * {@inheritdoc}
   */
  protected function getCurrentState() {
    try {
      $structure = [];
      
      // Get field definitions
      $field_definitions = $this->entityFieldManager
        ->getFieldDefinitions('node', 'course');

      // Build structure array
      $structure['fields'] = [];
      foreach ($field_definitions as $field_name => $definition) {
        $structure['fields'][$field_name] = [
          'type' => $definition->getType(),
          'label' => $definition->getLabel(),
          'required' => $definition->isRequired(),
          'settings' => $definition->getSettings(),
        ];
      }

      // Add taxonomy vocabulary info
      $structure['vocabularies'] = $this->getRelatedVocabularies();

      // Add configuration
      $structure['config'] = [
        'status' => $this->configFactory->get('node.type.course')->get('status'),
        'workflow' => $this->configFactory->get('workflows.workflow.course')->get(),
      ];

      // Add display settings
      $structure['displays'] = $this->getDisplaySettings();

      return $structure;
    }
    catch (\Exception $e) {
      $this->getLogger()->error('Failed to get course structure state: @error', ['@error' => $e->getMessage()]);
      return [];
    }
  }

  /**
   * Gets vocabularies related to courses.
   */
  protected function getRelatedVocabularies() {
    $vocabularies = [];
    
    try {
      $field_definitions = $this->entityFieldManager
        ->getFieldDefinitions('node', 'course');

      foreach ($field_definitions as $field_name => $definition) {
        if ($definition->getType() === 'entity_reference' && 
            $definition->getSetting('target_type') === 'taxonomy_term') {
          $handler_settings = $definition->getSetting('handler_settings');
          if (!empty($handler_settings['target_bundles'])) {
            foreach ($handler_settings['target_bundles'] as $vocab) {
              $vocabulary = $this->entityTypeManager
                ->getStorage('taxonomy_vocabulary')
                ->load($vocab);
              if ($vocabulary) {
                $vocabularies[$vocab] = $vocabulary->label();
              }
            }
          }
        }
      }
    }
    catch (\Exception $e) {
      $this->getLogger()->error('Error getting course vocabularies: @error', ['@error' => $e->getMessage()]);
    }

    return $vocabularies;
  }

  /**
   * Gets display settings for course entity.
   */
  protected function getDisplaySettings() {
    $displays = [];
    
    try {
      $view_displays = $this->entityTypeManager
        ->getStorage('entity_view_display')
        ->loadByProperties([
          'targetEntityType' => 'node',
          'bundle' => 'course',
        ]);

      foreach ($view_displays as $display) {
        if ($display instanceof LayoutBuilderEntityViewDisplay) {
          $displays[$display->getMode()] = [
            'layout_builder_enabled' => $display->isLayoutBuilderEnabled(),
            'sections' => $this->getLayoutSections($display),
          ];
        }
        else {
          $displays[$display->getMode()] = [
            'components' => $display->getComponents(),
          ];
        }
      }
    }
    catch (\Exception $e) {
      $this->getLogger()->error('Error getting display settings: @error', ['@error' => $e->getMessage()]);
    }

    return $displays;
  }

  /**
   * Gets layout sections from a Layout Builder display.
   *
   * @param \Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay $display
   *   The Layout Builder display.
   *
   * @return array
   *   Array of section data.
   */
  protected function getLayoutSections(LayoutBuilderEntityViewDisplay $display) {
    $sections = [];
    
    if (method_exists($display, 'getSections')) {
      /** @var \Drupal\layout_builder\Section[] $layout_sections */
      $layout_sections = $display->getSections();
      
      foreach ($layout_sections as $delta => $section) {
        $sections[$delta] = [
          'layout_id' => $section->getLayoutId(),
          'layout_settings' => $section->getLayoutSettings(),
          'components' => [],
        ];
        
        foreach ($section->getComponents() as $uuid => $component) {
          // Get component data using array access since the component is a plugin
          $plugin_config = $component->toArray();
          $sections[$delta]['components'][$uuid] = [
            'configuration' => $plugin_config['configuration'] ?? [],
            'region' => $plugin_config['region'] ?? '',
            'weight' => $plugin_config['weight'] ?? 0,
            'additional' => $plugin_config['additional'] ?? [],
          ];
        }
      }
    }
    
    return $sections;
  }

  /**
   * {@inheritdoc}
   */
  protected function calculateChanges(array $previous, array $current) {
    $changes = [];

    // Check field changes
    if (!empty($current['fields']) && !empty($previous['fields'])) {
      $changes = array_merge($changes, $this->calculateFieldChanges($previous['fields'], $current['fields']));
    }

    // Check vocabulary changes
    if (!empty($current['vocabularies']) && !empty($previous['vocabularies'])) {
      $changes = array_merge($changes, $this->calculateVocabularyChanges($previous['vocabularies'], $current['vocabularies']));
    }

    // Check display changes
    if (!empty($current['displays']) && !empty($previous['displays'])) {
      $changes = array_merge($changes, $this->calculateDisplayChanges($previous['displays'], $current['displays']));
    }

    return $changes;
  }

  /**
   * Calculate field changes.
   */
  protected function calculateFieldChanges(array $previous, array $current) {
    $changes = [];

    foreach ($current as $field_name => $field_info) {
      if (!isset($previous[$field_name])) {
        $changes[] = $this->t('New field added: @field', ['@field' => $field_name]);
        continue;
      }
      if ($previous[$field_name] !== $field_info) {
        $changes[] = $this->t('Field @field configuration changed', ['@field' => $field_name]);
      }
    }

    foreach ($previous as $field_name => $field_info) {
      if (!isset($current[$field_name])) {
        $changes[] = $this->t('Field removed: @field', ['@field' => $field_name]);
      }
    }

    return $changes;
  }

  /**
   * Calculate vocabulary changes.
   */
  protected function calculateVocabularyChanges(array $previous, array $current) {
    $changes = [];

    foreach ($current as $vocab_id => $vocab_name) {
      if (!isset($previous[$vocab_id])) {
        $changes[] = $this->t('New vocabulary reference added: @vocab', ['@vocab' => $vocab_name]);
      }
    }

    foreach ($previous as $vocab_id => $vocab_name) {
      if (!isset($current[$vocab_id])) {
        $changes[] = $this->t('Vocabulary reference removed: @vocab', ['@vocab' => $vocab_name]);
      }
    }

    return $changes;
  }

  /**
   * Calculate display changes.
   */
  protected function calculateDisplayChanges(array $previous, array $current) {
    $changes = [];

    foreach ($current as $display_id => $display_info) {
      if (!isset($previous[$display_id])) {
        $changes[] = $this->t('New display mode added: @mode', ['@mode' => $display_id]);
        continue;
      }
      if ($previous[$display_id] !== $display_info) {
        $changes[] = $this->t('Display mode @mode configuration changed', ['@mode' => $display_id]);
      }
    }

    foreach ($previous as $display_id => $display_info) {
      if (!isset($current[$display_id])) {
        $changes[] = $this->t('Display mode removed: @mode', ['@mode' => $display_id]);
      }
    }

    return $changes;
  }

  /**
   * Gets a summary of the course structure.
   */
  protected function getStructureSummary() {
    $structure = $this->getCurrentState();
    $summary = [];

    if (!empty($structure['fields'])) {
      $field_types = [];
      foreach ($structure['fields'] as $field) {
        $field_types[$field['type']] = ($field_types[$field['type']] ?? 0) + 1;
      }

      // ...existing code...
    }

    return $summary;
  }

}
