<?php

namespace Drupal\dh_certificate\StructureMonitor;

/**
 * Monitors changes in taxonomy structure.
 */
class TaxonomyStructureMonitor extends EntityStructureMonitorBase {

  /**
   * State key for taxonomy structure.
   */
  protected $stateKey = 'dh_certificate.taxonomy_structure';

  /**
   * {@inheritdoc}
   */
  public function updateState() {
    $current_state = $this->getCurrentState();
    $this->state->set($this->stateKey, $current_state);
    $this->state->set($this->stateKey . '_updated', time());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    $this->state->delete($this->stateKey);
    $this->state->delete($this->stateKey . '_updated');
    return $this;
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
          'hierarchy' => $vocab->getHierarchy(),
        ];
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
      if ($previous[$vid]['name'] !== $vocab['name']) {
        $changes[] = $this->t('Vocabulary renamed from @old to @new', [
          '@old' => $previous[$vid]['name'],
          '@new' => $vocab['name'],
        ]);
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