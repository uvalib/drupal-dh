<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Controller for certificate course management.
 */
class CourseController extends ControllerBase {

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
   * Displays an overview of certificate courses.
   *
   * @return array
   *   A render array representing the course overview page.
   */
  public function overview() {
    $build = [
      '#theme' => 'dh_certificate_course_list',
      '#title' => $this->t('Certificate Courses'),
      '#courses' => [],
      '#categories' => [],
      '#add_course' => [
        '#type' => 'link',
        '#title' => $this->t('Add Course'),
        '#url' => Url::fromRoute('dh_certificate.course_add'),
        '#attributes' => [
          'class' => ['button', 'button--action', 'button--primary'],
        ],
      ],
      '#attached' => [
        'library' => ['dh_certificate/course-list'],
      ],
    ];

    // Load and organize courses by category
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->sort('title')
      ->accessCheck(TRUE); // Explicitly set access checking
    
    $nids = $query->execute();
    
    if ($nids) {
      $courses = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($courses as $course) {
        $category = $this->getFieldValue($course, 'field_course_type', 'Other');
        $build['#categories'][$category][] = [
          'title' => $course->label(),
          'credits' => $this->getFieldValue($course, 'field_credits', 0),
          'code' => $this->getFieldValue($course, 'field_course_code'),
          'next_offered' => $this->getFieldValue($course, 'field_term'),
        ];
      }
    }

    return $build;
  }
}
