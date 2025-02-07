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

  /**
   * Gets the number of active users in the certificate program.
   *
   * @return int
   *   The number of active users.
   */
  public function getActiveUsers();

  /**
   * Gets recent activities in the certificate program.
   *
   * @param int $limit
   *   Optional limit for number of activities to return.
   *
   * @return array
   *   Array of recent activities.
   */
  public function getRecentActivities($limit = 10);

  /**
   * Gets the current system status information.
   *
   * @return array
   *   Array of system status information.
   */
  public function getSystemStatus();

  /**
   * Gets changes for a specific monitor.
   *
   * @param string $monitor_id
   *   The ID of the monitor.
   *
   * @return array
   *   Array of monitor changes.
   */
  public function getMonitorChanges($monitor_id);

  /**
   * Gets the last check time for a monitor.
   *
   * @param string $monitor_id
   *   The ID of the monitor.
   *
   * @return int
   *   Timestamp of last check.
   */
  public function getMonitorLastCheck($monitor_id);

  /**
   * Gets the structure data for a monitor.
   *
   * @param string $monitor_id
   *   The ID of the monitor.
   *
   * @return array
   *   Array of structure data.
   */
  public function getMonitorStructure($monitor_id);
}
