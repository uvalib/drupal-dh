<?php

namespace Drupal\dh_certificate\StructureMonitor;

/**
 * Monitors changes in user profile structure.
 */
class ProfileStructureMonitor extends EntityStructureMonitorBase {

  /**
   * {@inheritdoc}
   */
  public function getMonitorId() {
    return 'profile_structure';
  }

  /**
   * {@inheritdoc}
   */
  protected function getCurrentState() {
    try {
      $fields = $this->entityTypeManager
        ->getStorage('field_config')
        ->loadByProperties(['entity_type' => 'user']);
      
      $state = [];
      foreach ($fields as $field) {
        $state[$field->getName()] = [
          'type' => $field->getType(),
          'settings' => $field->getSettings(),
          'required' => $field->isRequired(),
        ];
      }
      
      return $state;
    }
    catch (\Exception $e) {
      $this->loggerFactory->get('dh_certificate')
        ->error('Failed to get profile structure state: @error', ['@error' => $e->getMessage()]);
      return [];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function updateState() {
    $current_state = $this->getCurrentState();
    $this->state->set('dh_certificate.profile_structure', $current_state);
    $this->state->set('dh_certificate.profile_structure_updated', time());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    $this->state->delete('dh_certificate.profile_structure');
    $this->state->delete('dh_certificate.profile_structure_updated');
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  protected function calculateChanges(array $previous, array $current) {
    $changes = [];

    // Check for added fields
    foreach ($current as $field_name => $field_info) {
      if (!isset($previous[$field_name])) {
        $changes[] = $this->t('New field added: @field', ['@field' => $field_name]);
        continue;
      }
      
      // Check for field type changes
      if ($previous[$field_name]['type'] !== $field_info['type']) {
        $changes[] = $this->t('Field type changed for @field: @old to @new', [
          '@field' => $field_name,
          '@old' => $previous[$field_name]['type'],
          '@new' => $field_info['type'],
        ]);
      }
      
      // Check for required status changes
      if ($previous[$field_name]['required'] !== $field_info['required']) {
        $changes[] = $this->t('Required status changed for @field', ['@field' => $field_name]);
      }
      
      // Check for settings changes
      if ($previous[$field_name]['settings'] !== $field_info['settings']) {
        $changes[] = $this->t('Settings changed for @field', ['@field' => $field_name]);
      }
    }

    // Check for removed fields
    foreach ($previous as $field_name => $field_info) {
      if (!isset($current[$field_name])) {
        $changes[] = $this->t('Field removed: @field', ['@field' => $field_name]);
      }
    }

    return $changes;
  }

}
