<?php

namespace Drupal\dh_certificate;

use Drupal\Core\Session\AccountInterface;

/**
 * Interface for certificate progress management.
 */
interface ProgressManagerInterface {

  /**
   * Gets progress for a user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account to check progress for.
   *
   * @return array
   *   Progress information.
   */
  public function getUserProgress(AccountInterface $account);

  /**
   * Gets progress for a user ID.
   *
   * @param int $uid
   *   The user ID.
   *
   * @return array
   *   Progress information.
   */
  public function getUserProgressById($uid);

  /**
   * Gets progress for a specific item.
   *
   * @param string $id
   *   The item ID to check progress for.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   *
   * @return array
   *   Progress information.
   */
  public function getProgress($id, AccountInterface $account);

  /**
   * Updates progress for an item.
   *
   * @param string $id
   *   The item ID.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   * @param array $progress
   *   The progress data to save.
   */
  public function updateProgress($id, AccountInterface $account, array $progress);

  /**
   * Gets progress for a course.
   *
   * @param \Drupal\dh_certificate\Entity\CourseInterface $course
   *   The course entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   *
   * @return array
   *   Progress information.
   */
  public function getCourseProgress(\Drupal\dh_certificate\Entity\CourseInterface $course, AccountInterface $account);

  /**
   * Updates progress for a course.
   *
   * @param \Drupal\node\NodeInterface $course
   *   The course entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   * @param array $progress
   *   The progress data to save.
   */
  public function updateCourseProgress(\Drupal\node\NodeInterface $course, AccountInterface $account, array $progress);

  /**
   * Gets all course progress.
   *
   * @return array
   *   All course progress information.
   */
  public function getAllCourseProgress();

  /**
   * Completes a course.
   *
   * @param string $course_id
   *   The course ID.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   */
  public function completeCourse($course_id, AccountInterface $account);

  /**
   * Gets student progress.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   *
   * @return array
   *   Student progress information.
   */
  public function getStudentProgress(AccountInterface $account);

  /**
   * Updates requirement progress.
   *
   * @param array $student_progress
   *   The student progress data.
   * @param string $requirement_id
   *   The requirement ID.
   * @param array $data
   *   The data to update.
   */
  public function updateRequirementProgress($student_progress, $requirement_id, array $data);

  /**
   * Gets progress data for all users.
   *
   * @return array
   *   Array of progress data for all users.
   */
  public function getAllProgress();

  /**
   * Gets all reports data.
   *
   * @return array
   *   Array of reports data.
   */
  public function getReports();

  /**
   * Gets all enrollment data.
   *
   * @return array
   *   Array of enrollment data.
   */
  public function getAllEnrollments();
}
