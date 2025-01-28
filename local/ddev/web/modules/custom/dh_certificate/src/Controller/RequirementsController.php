<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for certificate requirements management.
 */
class RequirementsController extends ControllerBase {

  /**
   * Gets a field value safely.
   */
  protected function getFieldValue($entity, $field_name, $default = NULL) {
    if ($entity->hasField($field_name) && !$entity->get($field_name)->isEmpty()) {
      return $entity->get($field_name)->value;
    }
    return $default;
  }

  /**
   * Displays an overview of certificate requirements.
   *
   * @return array
   *   A render array representing the requirements overview page.
   */
  public function overview() {
    $build = [
      '#theme' => 'dh_certificate_requirements',
      '#title' => $this->t('Certificate Requirements'),
      '#attached' => [
        'library' => ['dh_certificate/certificate-requirements'],
      ],
    ];

    // Load configuration
    $config = $this->config('dh_certificate.requirements');

    // Get core courses
    $core_course_ids = $config->get('core_courses') ?: [];
    $core_courses = [];
    if (!empty($core_course_ids)) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($core_course_ids);
      foreach ($nodes as $node) {
        $core_courses[] = [
          'title' => $node->label(),
          'credits' => $this->getFieldValue($node, 'field_credits', 0),
          'code' => $this->getFieldValue($node, 'field_course_code', ''),
        ];
      }
    }

    // Structure requirements data
    $build['#requirements'] = [
      'core_courses' => $core_courses,
      'elective_credits' => $config->get('elective_credits') ?: 12,
      'due_date' => $config->get('due_date'),
    ];

    return $build;
  }

}
