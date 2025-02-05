<?php

namespace Drupal\dh_certificate\RequirementType;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Plugin manager for requirement type plugins.
 */
class RequirementTypePluginManager extends DefaultPluginManager {

  /**
   * Constructs a RequirementTypePluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/RequirementType',
      $namespaces,
      $module_handler,
      'Drupal\dh_certificate\RequirementType\RequirementTypeInterface',
      'Drupal\dh_certificate\Annotation\RequirementType'
    );
    $this->alterInfo('requirement_type_info');
    $this->setCacheBackend($cache_backend, 'requirement_type_plugins');
  }

}
