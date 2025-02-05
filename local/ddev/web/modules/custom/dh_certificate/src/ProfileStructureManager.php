<?php

namespace Drupal\dh_certificate;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Monitors changes in certificate profile structure.
 */
class ProfileStructureManager implements StructureMonitorInterface {
  use StringTranslationTrait;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

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
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructs a new ProfileStructureManager.
   */
  public function __construct(
    EntityFieldManagerInterface $entity_field_manager,
    EntityTypeManagerInterface $entity_type_manager,
    StateInterface $state,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->state = $state;
    $this->loggerFactory = $logger_factory->get('dh_certificate');
  }

  /**
   * {@inheritdoc}
   */
  public function getChanges() {
    $changes = [];
    $current = $this->getCurrentStructure();
    $stored = $this->state->get('dh_certificate.profile_structure', []);

    // Compare field definitions
    foreach ($current as $field_name => $definition) {
      if (!isset($stored[$field_name])) {
        $changes[] = [
          'type' => 'ADDED',
          'entity_type' => 'profile',
          'entity_id' => $field_name,
          'description' => $this->t('Added field @field', ['@field' => $field_name]),
          'timestamp' => time(),
        ];
        continue;
      }

      if ($this->hasDefinitionChanged($stored[$field_name], $definition)) {
        $changes[] = [
          'type' => 'MODIFIED',
          'entity_type' => 'profile',
          'entity_id' => $field_name,
          'description' => $this->t('Modified field @field', ['@field' => $field_name]),
          'timestamp' => time(),
        ];
      }
    }

    // Check for removed fields
    foreach ($stored as $field_name => $definition) {
      if (!isset($current[$field_name])) {
        $changes[] = [
          'type' => 'REMOVED',
          'entity_type' => 'profile',
          'entity_id' => $field_name,
          'description' => $this->t('Removed field @field', ['@field' => $field_name]),
          'timestamp' => time(),
        ];
      }
    }

    return $changes;
  }

  /**
   * {@inheritdoc}
   */
  public function recordCurrentStructure() {
    $this->state->set('dh_certificate.profile_structure', $this->getCurrentStructure());
    $this->state->set('dh_certificate.profile_structure_updated', time());
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    $this->state->delete('dh_certificate.profile_structure');
    $this->state->delete('dh_certificate.profile_structure_updated');
    $this->recordCurrentStructure();
  }

  /**
   * Gets the current profile structure.
   *
   * @return array
   *   The current structure definition.
   */
  protected function getCurrentStructure() {
    $structure = [];
    
    try {
      $fields = $this->entityFieldManager->getFieldDefinitions('profile', 'certificate');
      foreach ($fields as $field_name => $definition) {
        $structure[$field_name] = [
          'type' => $definition->getType(),
          'label' => $definition->getLabel(),
          'required' => $definition->isRequired(),
          'settings' => $definition->getSettings(),
          'widget' => $this->getWidgetSettings($field_name),
        ];
      }
    }
    catch (\Exception $e) {
      $this->loggerFactory->error('Failed to get profile structure: @message', ['@message' => $e->getMessage()]);
      return [];
    }

    return $structure;
  }

  /**
   * Gets widget settings for a field.
   */
  protected function getWidgetSettings($field_name) {
    try {
      $form_display = $this->entityTypeManager
        ->getStorage('entity_form_display')
        ->load('profile.certificate.default');

      if ($form_display) {
        $component = $form_display->getComponent($field_name);
        return $component ? $component['settings'] : [];
      }
    }
    catch (\Exception $e) {
      $this->loggerFactory->error('Failed to get widget settings: @message', ['@message' => $e->getMessage()]);
    }
    
    return [];
  }

  /**
   * Checks if a field definition has changed.
   */
  protected function hasDefinitionChanged(array $old, array $new) {
    $keys = ['type', 'label', 'required', 'settings', 'widget'];
    foreach ($keys as $key) {
      if (!isset($old[$key]) || !isset($new[$key])) {
        return TRUE;
      }
      if ($old[$key] !== $new[$key]) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
