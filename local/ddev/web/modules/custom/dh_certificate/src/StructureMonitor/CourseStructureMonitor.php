<?php

namespace Drupal\dh_certificate\StructureMonitor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay;
use Drupal\layout_builder\Section;

/**
 * Monitors changes in course structure.
 */
class CourseStructureMonitor extends EntityStructureMonitorBase {

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
      // Get field definitions for course content type
      $fields = $this->entityTypeManager
        ->getStorage('field_config')
        ->loadByProperties([
          'entity_type' => 'node',
          'bundle' => 'course',
        ]);

      $state = [];
      
      // Add base fields
      $node_type = $this->entityTypeManager
        ->getStorage('node_type')
        ->load('course');
      
      if ($node_type) {
        $state['type'] = [
          'label' => $node_type->label(),
          'description' => $node_type->getDescription(),
        ];
      }

      // Add field configurations
      foreach ($fields as $field) {
        $state['fields'][$field->getName()] = [
          'type' => $field->getType(),
          'label' => $field->getLabel(),
          'required' => $field->isRequired(),
          'settings' => $field->getSettings(),
          'field_type' => $field->getType(),
          'cardinality' => $field->getFieldStorageDefinition()->getCardinality(),
        ];
      }

      // Get view display settings
      $view_displays = $this->entityTypeManager
        ->getStorage('entity_view_display')
        ->loadByProperties([
          'targetEntityType' => 'node',
          'bundle' => 'course',
        ]);

      foreach ($view_displays as $display) {
        if ($display instanceof LayoutBuilderEntityViewDisplay) {
          $state['displays'][$display->getMode()] = [
            'layout_builder_enabled' => $display->isLayoutBuilderEnabled(),
            'sections' => $this->getLayoutSections($display),
            'components' => $display->getComponents(),
          ];
        }
        else {
          $components = $display->getComponents();
          $state['displays'][$display->getMode()] = [
            'components' => array_map(function ($component) {
              return [
                'type' => $component['type'] ?? '',
                'weight' => $component['weight'] ?? 0,
                'settings' => $component['settings'] ?? [],
                'region' => $component['region'] ?? 'content',
              ];
            }, $components),
          ];
        }
      }

      // Get form display settings
      $form_displays = $this->entityTypeManager
        ->getStorage('entity_form_display')
        ->loadByProperties([
          'targetEntityType' => 'node',
          'bundle' => 'course',
        ]);

      foreach ($form_displays as $display) {
        $components = $display->getComponents();
        $state['form_displays'][$display->getMode()] = [
          'components' => array_map(function ($component) {
            return [
              'type' => $component['type'] ?? '',
              'weight' => $component['weight'] ?? 0,
              'settings' => $component['settings'] ?? [],
              'region' => $component['region'] ?? 'content',
            ];
          }, $components),
        ];
      }

      return $state;
    }
    catch (\Exception $e) {
      $this->loggerFactory->get('dh_certificate')
        ->error('Failed to get course structure state: @error', ['@error' => $e->getMessage()]);
      return [];
    }
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
    $this->changes = [];

    // Check type changes
    if (isset($current['type'], $previous['type'])) {
      if ($current['type']['label'] !== $previous['type']['label']) {
        $this->changes[] = $this->t('Course type label changed from @old to @new', [
          '@old' => $previous['type']['label'],
          '@new' => $current['type']['label'],
        ]);
      }
    }

    // Check field changes
    $current_fields = $current['fields'] ?? [];
    $previous_fields = $previous['fields'] ?? [];

    foreach ($current_fields as $field_name => $field_info) {
      if (!isset($previous_fields[$field_name])) {
        $changes[] = $this->t('New field added: @field', [
          '@field' => $field_name,
        ]);
        continue;
      }

      $prev_field = $previous_fields[$field_name];

      // Check field type changes
      if ($prev_field['type'] !== $field_info['type']) {
        $changes[] = $this->t('Field type changed for @field: @old to @new', [
          '@field' => $field_name,
          '@old' => $prev_field['type'],
          '@new' => $field_info['type'],
        ]);
      }

      // Check required status changes
      if ($prev_field['required'] !== $field_info['required']) {
        $changes[] = $this->t('Required status changed for @field', [
          '@field' => $field_name,
        ]);
      }

      // Check cardinality changes
      if ($prev_field['cardinality'] !== $field_info['cardinality']) {
        $changes[] = $this->t('Cardinality changed for @field', [
          '@field' => $field_name,
        ]);
      }
    }

    // Check for removed fields
    foreach ($previous_fields as $field_name => $field_info) {
      if (!isset($current_fields[$field_name])) {
        $changes[] = $this->t('Field removed: @field', [
          '@field' => $field_name,
        ]);
      }
    }

    return $this->changes;
  }

  /**
   * {@inheritdoc}
   */
  public function updateState() {
    $current_state = $this->getCurrentState();
    $this->state->set('dh_certificate.course_structure', $current_state);
    $this->state->set('dh_certificate.course_structure_updated', time());
    return $this;
  }

  /**
   * Checks if a course structure state exists.
   *
   * @return bool
   *   TRUE if state exists, FALSE otherwise.
   */
  public function hasState(): bool {
    try {
      $state = $this->state->get('dh_certificate.course_structure', NULL);
      return !empty($state);
    }
    catch (\Exception $e) {
      $this->logger->error('Failed to check course structure state: @error', [
        '@error' => $e->getMessage(),
      ]);
      return FALSE;
    }
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

  /**
   * {@inheritdoc}
   */
  public function reset() {
    $this->state->delete('dh_certificate.course_structure');
    $this->state->delete('dh_certificate.course_structure_updated');
    return $this;
  }

  /**
   * Gets the current course structure.
   *
   * @return array
   *   The current course structure data.
   */
  public function getCurrentStructure() {
    $structure = [];
    
    try {
      // Get course field definitions
      $field_definitions = \Drupal::service('entity_field.manager')
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

      // Add taxonomy vocabulary info if used
      if ($vocabularies = $this->getRelatedVocabularies()) {
        $structure['vocabularies'] = $vocabularies;
      }

      // Add any additional course configuration
      $structure['config'] = [
        'status' => \Drupal::config('node.type.course')->get('status'),
        'workflow' => \Drupal::config('workflows.workflow.course')->get(),
      ];

    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error('Error getting course structure: @error', [
        '@error' => $e->getMessage(),
      ]);
    }

    return $structure;
  }

  /**
   * Gets vocabularies related to courses.
   *
   * @return array
   *   Array of vocabulary information.
   */
  protected function getRelatedVocabularies() {
    $vocabularies = [];
    
    try {
      $field_definitions = \Drupal::service('entity_field.manager')
        ->getFieldDefinitions('node', 'course');

      foreach ($field_definitions as $field_name => $definition) {
        if ($definition->getType() === 'entity_reference' && 
            $definition->getSetting('target_type') === 'taxonomy_term') {
          $handler_settings = $definition->getSetting('handler_settings');
          if (!empty($handler_settings['target_bundles'])) {
            foreach ($handler_settings['target_bundles'] as $vocab) {
              $vocabularies[$vocab] = \Drupal::entityTypeManager()
                ->getStorage('taxonomy_vocabulary')
                ->load($vocab)
                ->label();
            }
          }
        }
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('dh_certificate')->error('Error getting course vocabularies: @error', [
        '@error' => $e->getMessage(),
      ]);
    }

    return $vocabularies;
  }

}
