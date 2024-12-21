<?php

namespace Drupal\dh_dashboard\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rules\Engine\RulesComponent;

/**
 * Service for handling certificate-related business logic.
 */
class CertificateService
{
    protected $entityTypeManager;

    public function __construct(EntityTypeManagerInterface $entity_type_manager)
    {
        $this->entityTypeManager = $entity_type_manager;
    }

    /**
     * Get the current progress of a certificate.
     *
     * @param int $user_id
     *   The user ID.
     *
     * @return array
     *   The progress data.
     */
    public function getCurrentProgress($user_id)
    {
        $progress = [
            'completed' => 5,
            'total' => 10,
        ];

        try {
            $rules_config = $this->entityTypeManager
                ->getStorage('rules_component')
                ->load('certificate_progress_rule');

            if ($rules_config) {
                /** @var \Drupal\rules\Engine\RulesComponent $component */
                $component = $rules_config->getComponent();
                $state = $component->getState();
                $state->setVariable('progress', $progress);
                $state->setVariable('user_id', $user_id);
                $component->execute();
                return $state->getVariable('progress');
            }
        }
        catch (\Exception $e) {
            \Drupal::logger('dh_dashboard')->error('Error executing rules: @message', ['@message' => $e->getMessage()]);
        }

        return $progress;
    }
}
