<?php

namespace Drupal\dh_certificate\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Custom Twig extension for DH Certificate module.
 */
class DHCertificateExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('pluralize', [$this, 'pluralize']),
    ];
  }

  /**
   * Pluralizes a string.
   *
   * @param string $string
   *   The string to pluralize.
   *
   * @return string
   *   The pluralized string.
   */
  public function pluralize($string) {
    // Simple pluralization rules
    $last_char = substr($string, -1);
    if ($last_char === 'y') {
      return substr($string, 0, -1) . 'ies';
    }
    if ($last_char === 's' || $last_char === 'x' || $last_char === 'z') {
      return $string . 'es';
    }
    return $string . 's';
  }

}
