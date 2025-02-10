<?php

namespace Drupal\dh_certificate\StructureMonitor;

/**
 * Monitors changes in taxonomy structure.
 */
class TaxonomyStructureMonitor extends EntityStructureMonitorBase {

  /**
   * {@inheritdoc}
   */
  public function updateState() {
    $current_state = $this->getCurrentState();
    // Use the stateKey from parent class
    $this->state->set($this->stateKey, $current_state);
    $this->state->set($this->stateKey . '_updated', time());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    // Use the stateKey from parent class
    $this->state->delete($this->stateKey);
    $this->state->delete($this->stateKey . '_updated');
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMonitorId() {
    return 'taxonomy_structure';
  }

  /**
   * Gets current taxonomy structure state.
   */
  protected function getCurrentState() {
    try {
      $vocabularies = $this->entityTypeManager
        ->getStorage('taxonomy_vocabulary')
        ->loadMultiple();

      $state = [];
      foreach ($vocabularies as $vocab) {
        $state[$vocab->id()] = [
          'name' => $vocab->label(),
          'description' => $vocab->getDescription(),
          'weight' => $vocab->get('weight'),
          'status' => $vocab->status(),
        ];

        // Get field definitions for this vocabulary
        $fields = $this->entityTypeManager
          ->getStorage('field_config')
          ->loadByProperties([
            'entity_type' => 'taxonomy_term',
            'bundle' => $vocab->id(),
          ]);

        // Add field information
        $state[$vocab->id()]['fields'] = [];
        foreach ($fields as $field) {
          $state[$vocab->id()]['fields'][$field->getName()] = [
            'type' => $field->getType(),
            'label' => $field->getLabel(),
            'required' => $field->isRequired(),
          ];
        }
      }
      return $state;
    }
    catch (\Exception $e) {
      $this->logger->error('Failed to get taxonomy structure state: @error', ['@error' => $e->getMessage()]);
      return [];
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function calculateChanges(array $previous, array $current) {
    $changes = [];
    
    foreach ($current as $vid => $vocab) {
      if (!isset($previous[$vid])) {
        $changes[] = $this->t('New vocabulary added: @name', ['@name' => $vocab['name']]);
        continue;
      }

      // Check basic properties
      if ($previous[$vid]['name'] !== $vocab['name']) {
        $changes[] = $this->t('Vocabulary renamed from @old to @new', [
          '@old' => $previous[$vid]['name'],
          '@new' => $vocab['name'],
        ]);
      }

      if ($previous[$vid]['description'] !== $vocab['description']) {
        $changes[] = $this->t('Description changed for vocabulary: @name', ['@name' => $vocab['name']]);
      }

      // Check fields
      foreach ($vocab['fields'] ?? [] as $field_name => $field) {
        if (!isset($previous[$vid]['fields'][$field_name])) {
          $changes[] = $this->t('New field @field added to vocabulary @vocab', [
            '@field' => $field_name,
            '@vocab' => $vocab['name'],
          ]);
          continue;
        }

        if ($previous[$vid]['fields'][$field_name] !== $field) {
          $changes[] = $this->t('Field @field configuration changed in vocabulary @vocab', [
            '@field' => $field_name,
            '@vocab' => $vocab['name'],
          ]);
        }
      }

      // Check for removed fields
      foreach ($previous[$vid]['fields'] ?? [] as $field_name => $field) {
        if (!isset($vocab['fields'][$field_name])) {
          $changes[] = $this->t('Field @field removed from vocabulary @vocab', [
            '@field' => $field_name,
            '@vocab' => $vocab['name'],
          ]);
        }
      }
    }

    foreach ($previous as $vid => $vocab) {
      if (!isset($current[$vid])) {
        $changes[] = $this->t('Vocabulary removed: @name', ['@name' => $vocab['name']]);
      }
    }

    return $changes;
  }
}