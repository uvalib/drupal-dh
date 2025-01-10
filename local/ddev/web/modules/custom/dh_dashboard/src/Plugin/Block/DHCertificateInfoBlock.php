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
      '#label_display' => 'FALSE',
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
      'status_class' => 'certificate-status--in-progress',
      'requirement_groups' => [
        [
          'name' => 'Core Courses',
          'completed' => 2,
          'required' => 4,
          'courses' => ['DH 101', 'DH 201'],
          'progress_class' => 'requirement-progress--partial',
          'icon' => 'book',
        ],
        [
          'name' => 'Electives',
          'completed' => 1,
          'required' => 2,
          'courses' => ['HIST 301'],
          'progress_class' => 'requirement-progress--partial',
          'icon' => 'book',
        ],
        [
          'name' => 'Capstone',
          'completed' => 0,
          'required' => 1,
          'courses' => [],
          'progress_class' => 'requirement-progress--none',
          'icon' => 'book',
        ],
      ],
      'attributes' => [
        'class' => ['dh-certificate-info', 'info-grid', 'block-spacing'],
      ],
    ];
  }
}