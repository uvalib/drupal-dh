<?php

namespace Drupal\dh_certificate\Schema;

/**
 * Manages certificate-specific extensions to the course schema.
 */
class CourseSchemaExtender {

  /**
   * Gets the certificate-specific field additions for courses.
   */
  public static function getRequiredFields() {
    return [
      'field_certificate_status' => [
        'type' => 'varchar',
        'length' => 32,
        'not null' => TRUE,
        'default' => 'pending',
        'description' => 'Certificate-specific course status',
      ],
      'field_certificate_requirements' => [
        'type' => 'text',
        'size' => 'big',
        'description' => 'Certificate requirements for this course',
      ],
    ];
  }

  /**
   * Checks if all required certificate fields exist in course schema.
   */
  public static function validateCourseSchema(array $existing_schema): array {
    $required = self::getRequiredFields();
    $missing = [];
    
    foreach ($required as $field => $spec) {
      if (!isset($existing_schema[$field])) {
        $missing[$field] = $spec;
      }
    }
    
    return $missing;
  }

  /**
   * Gets schema changes that need to be tracked.
   */
  public static function getSchemaChanges(array $old_schema, array $new_schema): array {
    $changes = [];
    $required = self::getRequiredFields();
    
    foreach ($required as $field => $spec) {
      if (isset($new_schema[$field]) && $new_schema[$field] !== $old_schema[$field]) {
        $changes[$field] = [
          'old' => $old_schema[$field] ?? NULL,
          'new' => $new_schema[$field],
        ];
      }
    }
    
    return $changes;
  }
}
