<?php

namespace Drupal\dh_certificate\RequirementType;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Traversable;

/**
 * Manages requirement type plugins.
 */
class RequirementTypeManager extends DefaultPluginManager implements RequirementTypeManagerInterface {
  
  use StringTranslationTrait;

  /**
   * Constructs a RequirementTypeManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation service.
   */
  public function __construct(
    Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler,
    TranslationInterface $string_translation
  ) {
    parent::__construct(
      'Plugin/RequirementType',
      $namespaces,
      $module_handler,
      'Drupal\dh_certificate\RequirementType\RequirementTypeInterface',
      'Drupal\dh_certificate\Annotation\RequirementType'
    );
    
    $this->setStringTranslation($string_translation);
    $this->setCacheBackend($cache_backend, 'requirement_type_plugins');
    $this->alterInfo('requirement_type_info');
  }

  /**
   * {@inheritdoc}
   */
  public function getRequirementType($type)
  {
    if (!isset($this->types[$type])) {
      // Load from annotation discovery
      $this->types[$type] = $this->createInstance($type);
    }
    return $this->types[$type];
  }

  /**
   * Creates a requirement type instance.
   *
   * @param string $plugin_id
   *   The ID of the plugin being instantiated.
   * @param array $configuration
   *   An array of configuration relevant to the plugin instance.
   *
   * @return \Drupal\dh_certificate\RequirementType\RequirementTypeInterface
   *   A fully configured plugin instance.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   If the instance cannot be created, such as if the ID is invalid.
   */
  public function createInstance($plugin_id, array $configuration = []) {
    switch ($plugin_id) {
      case 'course':
        return new CourseRequirement();
      case 'tool':
        return new ToolRequirement();
      default:
        throw new \InvalidArgumentException("Unknown requirement type: $plugin_id");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    $cache_key = 'dh_certificate:requirement_types';
    
    if ($cached = $this->cacheBackend->get($cache_key)) {
      return $cached->data;
    }

    $types = [
      'course' => [
        'id' => 'course',
        'label' => $this->t('Course Requirements'),
        'class' => CourseRequirement::class,
        'description' => $this->t('Course completion requirements'),
        'configurable' => TRUE,
      ],
      'tool' => [
        'id' => 'tool',
        'label' => $this->t('Tool Requirements'),
        'class' => ToolRequirement::class,
        'description' => $this->t('Tool proficiency requirements'),
        'configurable' => TRUE,
      ],
    ];

    $this->cacheBackend->set($cache_key, $types);
    return $types;
  }

  /**
   * {@inheritdoc}
   */
  public function hasDefinition($type) {
    $definitions = $this->getDefinitions();
    return isset($definitions[$type]);
  }

  /**
   * {@inheritdoc}
   */
  public function validateProgress(array $data)
  {
    // Implement logic to validate the progress data.
    // This is a placeholder implementation.
    return true;
  }

}