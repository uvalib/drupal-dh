<?php

namespace Drupal\dh_certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * @ConfigEntityType(
 *   id = "certificate_course",
 *   label = @Translation("Certificate Course"),
 *   handlers = {
 *     "list_builder" = "Drupal\dh_certificate\ListBuilder\CertificateCourseListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dh_certificate\Form\CertificateCourseForm",
 *       "edit" = "Drupal\dh_certificate\Form\CertificateCourseForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "certificate_course",
 *   admin_permission = "administer certificate courses",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "course_id",
 *     "type",
 *     "credits",
 *     "required"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/dh-certificate/courses/{certificate_course}",
 *     "delete-form" = "/admin/config/dh-certificate/courses/{certificate_course}/delete"
 *   }
 * )
 */
class CertificateCourse extends ConfigEntityBase {

  /**
   * The course node ID.
   *
   * @var int
   */
  protected $course_id;

  /**
   * The certificate course type (core, elective, etc).
   *
   * @var string
   */
  protected $type;

  /**
   * The number of credits.
   *
   * @var int
   */
  protected $credits;

  /**
   * The timestamp when this mapping was last verified.
   *
   * @var int
   */
  protected $last_verified;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    $this->last_verified = \Drupal::time()->getRequestTime();
  }

  /**
   * Verifies if the referenced course still exists and is valid.
   *
   * @return bool
   *   TRUE if valid, FALSE if needs update/removal
   */
  public function verifyReference() {
    $node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->load($this->course_id);
    
    return $node && $node->getType() === 'course' && $node->isPublished();
  }

  // Getters and setters...
}
