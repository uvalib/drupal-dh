<?php

namespace Drupal\dh_certificate;

use Drupal\Core\Session\AccountInterface;

/**
 * Interface for certificate management operations.
 */
interface CertificateManagerInterface {

  /**
   * Gets the certificate progress for a user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account to check progress for.
   *
   * @return array
   *   An array containing the certificate progress data.
   */
  public function getCertificateProgress(AccountInterface $account): array;

  /**
   * Checks if a specific course is completed.
   *
   * @param string $course_number
   *   The course number to check.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account to check for.
   *
   * @return bool
   *   TRUE if the course is completed, FALSE otherwise.
   */
  public function checkCourseCompletion(string $course_number, AccountInterface $account): bool;

  /**
   * Checks if a general requirement is completed.
   *
   * @param string $requirement_name
   *   The requirement name to check.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account to check for.
   *
   * @return bool
   *   TRUE if the requirement is completed, FALSE otherwise.
   */
  public function checkGeneralRequirement(string $requirement_name, AccountInterface $account): bool;
}
