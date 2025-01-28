<?php

namespace Drupal\dh_certificate\TwigExtension;

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
      new TwigFilter('json_tree', [$this, 'jsonTree'], ['is_safe' => ['html']]),
    ];
  }

  /**
   * Converts a JSON or array into a collapsible HTML tree.
   */
  public function jsonTree($value) {
    if (is_string($value)) {
      $data = json_decode($value, TRUE);
    } else {
      $data = $value;
    }
    return '<div class="json-tree">' . $this->arrayToHtml($data) . '</div>';
  }

  /**
   * Recursively converts an array to HTML.
   */
  protected function arrayToHtml($array) {
    if (!is_array($array)) {
      return $this->formatValue($array);
    }

    $output = '<ul>';
    foreach ($array as $key => $value) {
      $output .= '<li>';
      $output .= '<span class="key">' . $this->humanize($key) . ':</span> ';
      
      if (is_array($value)) {
        if (empty($value)) {
          $output .= '<span class="empty">empty</span>';
        } else {
          $output .= '<details><summary>' . $this->humanize($key) . '</summary>' . $this->arrayToHtml($value) . '</details>';
        }
      } else {
        $output .= '<span class="value">' . $this->formatValue($value) . '</span>';
      }
      
      $output .= '</li>';
    }
    $output .= '</ul>';
    
    return $output;
  }

  /**
   * Formats a single value for display.
   */
  protected function formatValue($value) {
    if ($value === NULL) {
      return '<span class="null">null</span>';
    }
    if (is_bool($value)) {
      return '<span class="boolean">' . ($value ? 'true' : 'false') . '</span>';
    }
    if (is_numeric($value)) {
      return '<span class="number">' . $value . '</span>';
    }
    if (is_string($value)) {
      if (empty($value)) {
        return '<span class="empty">empty string</span>';
      }
      return '<span class="string">' . htmlspecialchars($value) . '</span>';
    }
    if (is_object($value)) {
      return '<span class="object">' . get_class($value) . '</span>';
    }
    return '<span class="unknown">' . gettype($value) . '</span>';
  }

  /**
   * Converts a key to a human-readable label.
   */
  protected function humanize($key) {
    return ucwords(str_replace('_', ' ', $key));
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'dh_certificate_twig_extension';
  }
}
