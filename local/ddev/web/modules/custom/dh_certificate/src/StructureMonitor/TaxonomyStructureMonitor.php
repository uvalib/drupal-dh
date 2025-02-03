<?php

namespace Drupal\dh_certificate\StructureMonitor;

/**
 * Monitors changes in taxonomy structure.
 */
class TaxonomyStructureMonitor extends EntityStructureMonitorBase {

  /**
   * {@inheritdoc}
   */
  public function getMonitorId() {
    return 'taxonomy_structure';
  }

  /**
   * {@inheritdoc}
   */
  protected function getCurrentState() {
    try {
      $vocabularies = $this->entityTypeManager
        ->getStorage('taxonomy_vocabulary')
        ->loadMultiple();
      
      $state = [];
      foreach ($vocabularies as $vid => $vocabulary) {
        // Get fields for this vocabulary
        $fields = $this->entityTypeManager
          ->getStorage('field_config')
          ->loadByProperties([
            'entity_type' => 'taxonomy_term',
            'bundle' => $vid,
          ]);
        
        $field_data = [];
        foreach ($fields as $field) {
          $field_data[$field->getName()] = [
            'type' => $field->getType(),
            'required' => $field->isRequired(),
            'settings' => $field->getSettings(),
          ];
        }

        $state[$vid] = [
          'name' => $vocabulary->label(),
          'description' => $vocabulary->getDescription(),
          'fields' => $field_data,
        ];
      }
      
      return $state;
    }
    catch (\Exception $e) {
      $this->loggerFactory->get('dh_certificate')
        ->error('Failed to get taxonomy structure state: @error', ['@error' => $e->getMessage()]);
      return [];
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function calculateChanges(array $previous, array $current) {
    $changes = [];

    // Check for vocabulary changes
    foreach ($current as $vid => $vocab_info) {
      if (!isset($previous[$vid])) {
        $changes[] = $this->t('New vocabulary added: @vocab', [
          '@vocab' => $vocab_info['name'],
        ]);
        continue;
      }

      // Check for vocabulary name changes
      if ($previous[$vid]['name'] !== $vocab_info['name']) {
        $changes[] = $this->t('Vocabulary name changed for @vid: @old to @new', [
          '@vid' => $vid,
          '@old' => $previous[$vid]['name'],
          '@new' => $vocab_info['name'],
        ]);
      }

      // Check field changes
      foreach ($vocab_info['fields'] as $field_name => $field_info) {
        if (!isset($previous[$vid]['fields'][$field_name])) {
          $changes[] = $this->t('New field @field added to vocabulary @vocab', [
            '@field' => $field_name,
            '@vocab' => $vocab_info['name'],
          ]);
          continue;
        }

        $prev_field = $previous[$vid]['fields'][$field_name];
        
        // Check field type changes
        if ($prev_field['type'] !== $field_info['type']) {
          $changes[] = $this->t('Field type changed for @field in @vocab: @old to @new', [
            '@field' => $field_name,
            '@vocab' => $vocab_info['name'],
            '@old' => $prev_field['type'],
            '@new' => $field_info['type'],
          ]);
        }

        // Check required status changes
        if ($prev_field['required'] !== $field_info['required']) {
          $changes[] = $this->t('Required status changed for field @field in @vocab', [
            '@field' => $field_name,
            '@vocab' => $vocab_info['name'],
          ]);
        }
      }

      // Check for removed fields
      foreach ($previous[$vid]['fields'] as $field_name => $field_info) {
        if (!isset($vocab_info['fields'][$field_name])) {
          $changes[] = $this->t('Field @field removed from vocabulary @vocab', [
            '@field' => $field_name,
            '@vocab' => $vocab_info['name'],
          ]);
        }
      }
    }

    // Check for removed vocabularies
    foreach ($previous as $vid => $vocab_info) {
      if (!isset($current[$vid])) {
        $changes[] = $this->t('Vocabulary removed: @vocab', [
          '@vocab' => $vocab_info['name'],
        ]);
      }
    }

    return $changes;
  }

}
