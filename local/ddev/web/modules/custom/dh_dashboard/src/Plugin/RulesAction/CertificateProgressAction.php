<?php

namespace Drupal\dh_dashboard\Plugin\RulesAction;

use Drupal\rules\Core\RulesActionBase;

/**
 * @RulesAction(
 *   id = "certificate_progress",
 *   label = @Translation("Process certificate progress"),
 *   category = @Translation("Certificate"),
 *   context_definitions = {
 *     "progress" = @ContextDefinition("any",
 *       label = @Translation("Progress data")
 *     ),
 *     "user_id" = @ContextDefinition("integer",
 *       label = @Translation("User ID")
 *     )
 *   }
 * )
 */
class CertificateProgressAction extends RulesActionBase {

  /**
   * Execute the action.
   */
  protected function doExecute($progress, $user_id) {
    // Add your certificate progress logic here
    $progress['processed'] = TRUE;
    return $progress;
  }

}
