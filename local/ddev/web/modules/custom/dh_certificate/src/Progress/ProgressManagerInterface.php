<?php

namespace Drupal\dh_certificate\Progress;

use Drupal\Core\Session\AccountInterface;

/**
 * Interface for the progress manager service.
 */
interface ProgressManagerInterface {

  /**
   * Gets progress for a specific user.
   *
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   The user account to get progress for, or null for current user.
   *
   * @return array
   *   Array containing progress data:
   *   - total_courses: int
   *   - completed_courses: int
   *   - total_percentage: int
   *   - pending_actions: int
   *   - courses: array of course data
   */
  public function getUserProgress(?AccountInterface $account = NULL);

  /**
   * Gets all progress data.
   *
   * @return array
   *   Array of all progress data.
   */
  public function getAllProgress();

  /**
   * Gets the total number of enrolled students.
   *
   * @return int
   *   The total number of students.
   */
  public function getTotalStudents();

  /**
   * Gets the number of completed courses.
   *
   * @return int
   *   The number of completed courses.
   */
  public function getCompletedCoursesCount();

  /**
   * Gets the number of active courses.
   *
   * @return int
   *   The number of active courses.
   */
  public function getActiveCourses();

  /**
   * Gets the list of core courses.
   *
   * @return array
   *   Array of core course data.
   */
  public function getCoreCourses();

  /**
   * Gets the required number of elective credits.
   *
   * @return int
   *   Number of required elective credits.
   */
  public function getRequiredElectiveCredits();

  /**
   * Gets the completion deadline configuration.
   *
   * @return array
   *   Completion deadline configuration.
   */
  public function getCompletionDeadline();
}
