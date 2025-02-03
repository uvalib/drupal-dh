<?php

namespace Drupal\dh_certificate\Schema;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Monitors changes in the Course content type schema.
 */
class CourseSchemaMonitor {

  protected $entityFieldManager;
  protected $entityTypeManager;
  protected $logger;

  public function __construct(
    EntityFieldManagerInterface $entity_field_manager,
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger_factory->get('dh_certificate');
  }

  /**
   * Checks for changes in course fields we depend on.
   *
   * @return array
   *   Array of issues found, empty if everything is OK.
   */
  public function checkDependencies() {
    $issues = [];
    $fields = $this->entityFieldManager->getFieldDefinitions('node', 'course');
    
    // List of fields we depend on
    $required_fields = [
      'field_course_code' => 'string',
      'field_credits' => 'integer',
      'field_course_status' => 'list_string',
    ];

    foreach ($required_fields as $field_name => $expected_type) {
      if (!isset($fields[$field_name])) {
        $issues[] = "Required field '$field_name' is missing";
      }
      elseif ($fields[$field_name]->getType() !== $expected_type) {
        $issues[] = "Field '$field_name' has unexpected type: {$fields[$field_name]->getType()} (expected $expected_type)";
      }
    }

    return $issues;
  }

  /**
   * Gets recommendations for fixing schema issues.
   */
  public function getRecommendations(array $issues) {
    $recommendations = [];
    foreach ($issues as $issue) {
      if (str_contains($issue, 'missing')) {
        $recommendations[] = "Run update hooks to add missing fields";
      }
      elseif (str_contains($issue, 'unexpected type')) {
        $recommendations[] = "Data migration may be required - contact the Course module maintainer";
      }
    }
    return $recommendations;
  }
}