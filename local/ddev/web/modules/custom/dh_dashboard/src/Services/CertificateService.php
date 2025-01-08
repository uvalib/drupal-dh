<?php

namespace Drupal\dh_dashboard\Services;

use Drupal\Core\Session\AccountInterface;
use Drupal\dh_certificate\CertificateManagerInterface;

/**
 * Service for handling certificate-related business logic.
 */
class CertificateService
{
    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;

    /**
     * The certificate manager service.
     *
     * @var \Drupal\dh_certificate\CertificateManagerInterface
     */
    protected $certificateManager;

    public function __construct(
        AccountInterface $current_user,
        CertificateManagerInterface $certificate_manager
    ) {
        $this->currentUser = $current_user;
        $this->certificateManager = $certificate_manager;
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
        return [
            'completed' => 5,
            'total' => 10,
        ];
    }
}
