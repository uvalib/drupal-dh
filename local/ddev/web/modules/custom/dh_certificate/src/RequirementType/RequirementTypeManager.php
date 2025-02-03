<?php

namespace Drupal\dh_certificate\RequirementType;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\TypedData\TypedDataManagerInterface;

/**
 * Manages discovery and instantiation of requirement types.
 */
class RequirementTypeManager implements RequirementTypeManagerInterface
{

    /**
     * The module handler.
     *
     * @var \Drupal\Core\Extension\ModuleHandlerInterface
     */
    protected $moduleHandler;

    /**
     * The cache backend.
     *
     * @var \Drupal\Core\Cache\CacheBackendInterface
     */
    protected $cacheBackend;

    /**
     * The typed data manager.
     *
     * @var \Drupal\Core\TypedData\TypedDataManagerInterface
     */
    protected $typedDataManager;

    /**
     * The requirement types.
     *
     * @var array
     */
    protected $types = [];

    /**
     * Constructs a RequirementTypeManager object.
     *
     * @param \Drupal\Core\Extension\ModuleHandlerInterface    $module_handler
     *   The module handler.
     * @param \Drupal\Core\Cache\CacheBackendInterface         $cache_backend
     *   The cache backend.
     * @param \Drupal\Core\TypedData\TypedDataManagerInterface $typed_data_manager
     *   The typed data manager.
     */
    public function __construct(
        ModuleHandlerInterface $module_handler,
        CacheBackendInterface $cache_backend,
        TypedDataManagerInterface $typed_data_manager
    ) {
        $this->moduleHandler = $module_handler;
        $this->cacheBackend = $cache_backend;
        $this->typedDataManager = $typed_data_manager;
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
     */
    protected function createInstance($type)
    {
        switch ($type) {
            case 'course':
                return new CourseRequirement();
            case 'tool':
                return new ToolRequirement();
            default:
                throw new \InvalidArgumentException("Unknown requirement type: $type");
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