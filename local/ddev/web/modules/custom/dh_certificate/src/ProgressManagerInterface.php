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

}
