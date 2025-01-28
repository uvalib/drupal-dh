<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
          'title' => $this->formatCourseTitle($course),
          'credits' => $this->getFieldValue($course, 'field_credits', 0),
          'code' => $this->getFieldValue($course, 'field_course_code'),
          'next_offered' => $this->getFieldValue($course, 'field_term'),
        ];
      }
    }

    return $build;
  }

  /**
   * Displays the course listing page.
   *
   * @return array
   *   A render array representing the course listing page.
   */
  public function courseListing() {
    $config = $this->config('dh_certificate.settings');
    
    // Get full course data
    $course_data = [
      'count' => $this->entityTypeManager()->getStorage('node')->getQuery()
        ->condition('type', 'course')
        ->accessCheck(TRUE)
        ->count()
        ->execute(),
      'fields' => $this->getAvailableCourseFields(),
      'courses' => $this->getAllCoursesData(),
      'sample' => $this->getFirstCourse(),
    ];

    // Use Drupal's debug function instead of kint
    if ($config->get('show_debug')) {
      \Drupal::logger('dh_certificate')->debug('<pre>@data</pre>', [
        '@data' => print_r($course_data, TRUE),
      ]);
    }

    return [
      '#theme' => 'dh_certificate_course_listing',
      '#attached' => [
        'library' => [
          'dh_certificate/course-listing',
          'dh_certificate/debug-tree',
        ],
        'drupalSettings' => [
          'dhCertificate' => [
            'defaultView' => $config->get('default_view') ?: 'grid',
            'itemsPerPage' => (int) ($config->get('items_per_page') ?: 12),
            'debug' => (bool) $config->get('show_debug'),
            'paths' => [
              'ajax' => Url::fromRoute('dh_certificate.courses.ajax')->setAbsolute()->toString(),
              'add' => Url::fromRoute('dh_certificate.course_add')->setAbsolute()->toString(),
            ],
          ],
        ],
      ],
      '#default_view' => $config->get('default_view') ?: 'grid',
      '#items_per_page' => $config->get('items_per_page') ?: 12,
      '#course_data' => $course_data,
    ];
  }

  /**
   * Gets available course fields.
   */
  protected function getAvailableCourseFields() {
    $fields = [];
    $bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'course');
    foreach ($bundle_fields as $field_name => $field_definition) {
      $fields[$field_name] = [
        'type' => $field_definition->getType(),
        'label' => $field_definition->getLabel(),
      ];
    }
    return $fields;
  }

  /**
   * Gets complete data for all courses.
   */
  protected function getAllCoursesData() {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->accessCheck(TRUE)
      ->sort('title')
      ->execute();

    $courses = [];
    if (!empty($query)) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($query);
      foreach ($nodes as $node) {
        $courses[] = [
          'nid' => $node->id(),
          'title' => $node->getTitle(),
          'created' => $node->getCreatedTime(),
          'changed' => $node->getChangedTime(),
          'status' => $node->isPublished(),
          'fields' => $this->extractCourseValues($node),
        ];
      }
    }

    return $courses;
  }

  /**
   * Gets the first course for debugging purposes.
   */
  protected function getFirstCourse() {
    $nids = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->accessCheck(TRUE)
      ->range(0, 1)
      ->execute();
    
    if (!empty($nids)) {
      $node = $this->entityTypeManager()->getStorage('node')->load(reset($nids));
      return [
        'nid' => $node->id(),
        'uuid' => $node->uuid(),
        'title' => $node->getTitle(),
        'created' => $node->getCreatedTime(),
        'changed' => $node->getChangedTime(),
        'status' => $node->isPublished(),
        'author' => $node->getOwner()->getDisplayName(),
        'fields' => $this->extractCourseValues($node),
        'url' => $node->toUrl()->toString(),
      ];
    }
    return NULL;
  }

  /**
   * Extracts all relevant values from a course node.
   */
  protected function extractCourseValues($node) {
    $values = [];
    $fields = $this->getAvailableCourseFields();
    
    foreach (array_keys($fields) as $field_name) {
      if ($node->hasField($field_name) && !$node->get($field_name)->isEmpty()) {
        $field = $node->get($field_name);
        $field_type = $field->getFieldDefinition()->getType();

        switch ($field_type) {
          case 'entity_reference':
            $referenced_entities = $field->referencedEntities();
            $values[$field_name] = array_map(function($entity) {
              if ($entity) {
                return [
                  'id' => $entity->id(),
                  'label' => $entity->label(),
                  'bundle' => $entity->bundle(),
                ];
              }
              return NULL;
            }, $referenced_entities);
            break;

          case 'text_long':
          case 'text_with_summary':
            $values[$field_name] = $field->getValue()[0];
            break;

          default:
            $values[$field_name] = $field->getValue();
        }
      }
    }
    
    return $values;
  }

  public function ajaxCourseListing(Request $request) {
    $view = $request->query->get('view', 'grid');
    $page = (int) $request->query->get('page', 0);
    $search = $request->query->get('search', '');
    
    // Get filter as array, defaulting to empty array if not present
    $filter = [];
    if ($request->query->has('filter')) {
      $filterParam = $request->query->all()['filter'];
      if (is_array($filterParam)) {
        $filter = $filterParam;
      } elseif (is_string($filterParam)) {
        $filter = ['type' => $filterParam];
      }
    }

    $courses = $this->getCourses($search, $filter, $page);

    return new JsonResponse([
      'courses' => $courses,
      'total' => count($courses),
    ]);
  }

  private function getCourses($search, $filter, $page) {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->accessCheck(TRUE);

    // Add search condition
    if (!empty($search)) {
      $group = $query->orConditionGroup()
        ->condition('title', '%' . $this->entityTypeManager()->getStorage('node')->escapeLike($search) . '%', 'LIKE')
        ->condition('field_course_code', '%' . $this->entityTypeManager()->getStorage('node')->escapeLike($search) . '%', 'LIKE');
      $query->condition($group);
    }

    // Add filter conditions
    if (!empty($filter['type'])) {
      $query->condition('field_course_type', $filter['type']);
    }

    // Add pagination
    $items_per_page = $this->config('dh_certificate.settings')->get('items_per_page') ?? 12;
    $query->range($page * $items_per_page, $items_per_page);
    
    $nids = $query->execute();
    $courses = [];
    
    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        // Add debug logging
        \Drupal::logger('dh_certificate')->debug('Processing course: @title', [
          '@title' => $node->getTitle(),
        ]);
        
        $courses[] = [
          'id' => $node->id(),
          'title' => $this->formatCourseTitle($node),
          'code' => $this->getFieldValue($node, 'field_course_code', ''),
          'credits' => (int) $this->getFieldValue($node, 'field_credits', 0),
          'type' => $this->getFieldValue($node, 'field_course_type', ''),
          'term' => $this->getFieldValue($node, 'field_term', ''),
          'instructor' => $this->getFieldValue($node, 'field_course_instructor', ''),
          'description' => $this->getFieldValue($node, 'field_short_text_description', ''),
          'status' => $this->getFieldValue($node, 'field_status', 'not_started'),
          'department' => $this->getFieldValue($node, 'field_department', ''),
          'prerequisites' => $this->getFieldValue($node, 'field_prerequisites', ''),
          'url' => $node->toUrl()->toString(),
        ];
      }

      // Add debug logging
      \Drupal::logger('dh_certificate')->debug('Found @count courses', [
        '@count' => count($courses),
      ]);
    }
    
    return $courses;
  }

  protected function formatCourseTitle($node) {
    $mnemonic = $this->getFieldValue($node, 'field_course_mnemonic');
    $title = $node->label();
    
    return $mnemonic ? sprintf('%s: %s', $mnemonic, $title) : $title;
  }

  /**
   * Gets available course options for a select list.
   */
  public function getCourseOptions() {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'course')
      ->sort('field_course_mnemonic')
      ->sort('title')
      ->accessCheck(TRUE);
    
    $nids = $query->execute();
    $options = [];
    
    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $options[$node->id()] = $this->formatCourseTitle($node);
      }
    }
    
    return $options;
  }
}
