<?php

namespace Drupal\dh_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a DH Certificate Info Block.
 *
 * @Block(
 *   id = "dh_dashboard_certificate_info",
 *   admin_label = @Translation("Certificate Info"),
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
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  protected function getCertificateInfo() {
    return [
      'name' => 'Digital Humanities Certificate',
      'status' => 'In Progress',
      'completion' => '32%',
      'requirement_groups' => [
        [
          'name' => 'Core Courses',
          'completed' => 2,
          'required' => 4,
          'courses' => ['DH 101', 'DH 201'],
        ],
        [
          'name' => 'Electives',
          'completed' => 1,
          'required' => 2,
          'courses' => ['HIST 301'],
        ],
        [
          'name' => 'Capstone',
          'completed' => 0,
          'required' => 1,
          'courses' => [],
        ],
      ],
    ];
  }
}