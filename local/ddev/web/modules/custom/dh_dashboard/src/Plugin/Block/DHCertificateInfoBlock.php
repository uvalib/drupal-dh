<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a DH Certificate Info Block.
 *
 * @Block(
 *   id = "dh_dashboard_certificate_info",
 *   admin_label = @Translation("Certificate Information"),
 *   category = @Translation("DH Dashboard")
 * )
 */
class DHCertificateInfoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'dh_dashboard_certificate_info',
      '#info' => $this->getCertificateInfo(),
    ];
  }

  protected function getCertificateInfo() {
    // Mock data - replace with actual certificate information
    return [
      'requirements' => [
        'Core Courses' => 3,
        'Electives' => 3,
        'Final Project' => 1,
      ],
      'announcements' => [
        'Next application deadline: March 1st, 2024',
        'Certificate orientation: January 20th, 2024',
      ],
    ];
  }
}