<?php

namespace Drupal\dh_certificate\Schema;

/**
 * Provides schema definitions for DH Certificate module.
 */
class CertificateSchema {

  /**
   * Get the enrollment table schema definition.
   *
   * @return array
   *   The schema definition.
   */
  public static function getEnrollmentSchema() {
    return [
      'description' => 'Stores certificate course enrollment data.',
      'fields' => [
        'id' => [
          'type' => 'serial',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'description' => 'Primary Key: Unique enrollment ID.',
        ],
        'uid' => [
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'description' => 'The {users}.uid that enrolled.',
        ],
        'course_id' => [
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'description' => 'The {node}.nid of the course.',
        ],
        'status' => [
          'type' => 'varchar',
          'length' => 32,
          'not null' => TRUE,
          'default' => 'enrolled',
          'description' => 'The enrollment status (enrolled, completed, withdrawn).',
        ],
        'enrolled_date' => [
          'type' => 'int',
          'not null' => TRUE,
          'description' => 'The Unix timestamp when the enrollment was created.',
        ],
        'completed_date' => [
          'type' => 'int',
          'not null' => FALSE,
          'description' => 'The Unix timestamp when the course was completed.',
        ],
      ],
      'primary key' => ['id'],
      'indexes' => [
        'uid' => ['uid'],
        'course_id' => ['course_id'],
        'status' => ['status'],
      ],
      'foreign keys' => [
        'enrolled_user' => [
          'table' => 'users',
          'columns' => ['uid' => 'uid'],
        ],
        'enrolled_course' => [
          'table' => 'node',
          'columns' => ['course_id' => 'nid'],
        ],
      ],
    ];
  }

  /**
   * Validates if the given schema matches our definition.
   *
   * @param array $schema
   *   The schema to validate.
   * 
   * @return bool
   *   TRUE if schemas match, FALSE otherwise.
   */
  public static function validateSchema(array $schema) {
    $expected = static::getEnrollmentSchema();
    return self::compareSchemas($expected, $schema);
  }

  /**
   * Deep comparison of two schema definitions.
   */
  protected static function compareSchemas(array $a, array $b) {
    if (array_keys($a) !== array_keys($b)) {
      return FALSE;
    }
    
    foreach ($a as $key => $value) {
      if (is_array($value)) {
        if (!is_array($b[$key]) || !self::compareSchemas($value, $b[$key])) {
          return FALSE;
        }
      }
      elseif ($value !== $b[$key]) {
        return FALSE;
      }
    }
    
    return TRUE;
  }
}
