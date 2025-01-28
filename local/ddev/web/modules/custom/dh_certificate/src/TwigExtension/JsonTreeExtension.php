<?php

namespace Drupal\dh_certificate\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Custom Twig extension for JSON tree rendering.
 */
class JsonTreeExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('json_tree', [$this, 'jsonTree'], ['is_safe' => ['html']]),
      new TwigFilter('is_numeric', [$this, 'isNumeric']), // Add this line
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
      // Skip numeric keys to avoid unnecessary nesting
      if (is_numeric($key)) {
        $output .= $this->arrayToHtml($value);
        continue;
      }

      $output .= '<li>';
      
      if (is_array($value)) {
        if (empty($value)) {
          $output .= '<span class="key">' . $this->humanize($key) . ':</span> <span class="empty">empty</span>';
        } else {
          $summary = $this->getSummary($key, $value);
          $output .= '<details><summary>' . $summary . '</summary>' . $this->arrayToHtml($value) . '</details>';
        }
      } else {
        $output .= '<span class="key">' . $this->humanize($key) . ':</span> <span class="value">' . $this->formatValue($value) . '</span>';
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
   * Generates a summary for an array.
   */
  protected function getSummary($key, $array) {
    $summary = '<span class="key">' . $this->humanize($key) . ':</span>';
    $summary .= '<span class="array-info">' . count($array) . ' items</span>';
    $preview = $this->getPreview($array);
    if ($preview) {
      $summary .= '<span class="preview"> ' . $preview . '</span>';
    }
    return $summary;
  }

  /**
   * Generates a preview for an array.
   */
  protected function getPreview($array) {
    $preview = [];
    foreach ($array as $key => $value) {
      if (!is_array($value)) {
        $preview[] = $this->formatValue($value);
      }
      if (count($preview) >= 3) {
        break;
      }
    }
    return '[' . implode(', ', $preview) . ']';
  }

  /**
   * Checks if a value is numeric.
   */
  public function isNumeric($value) {
    return is_numeric($value);
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'json_tree_extension';
  }
}