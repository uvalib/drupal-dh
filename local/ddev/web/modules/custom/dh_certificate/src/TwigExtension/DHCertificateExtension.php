<?php

namespace Drupal\dh_certificate\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DHCertificateExtension extends AbstractExtension {
  
  public function getFilters() {
    return [
      new TwigFilter('json_tree', [$this, 'jsonTree'], ['is_safe' => ['html']]),
    ];
  }

  public function jsonTree($value) {
    return '<div class="debug-tree">' . $this->arrayToHtml($value) . '</div>';
  }

  protected function arrayToHtml($data, $key = NULL) {
    if (is_array($data)) {
      $label = '';
      if (isset($data['title'])) {
        $label = ': ' . $data['title'];
      } elseif (isset($data['name'])) {
        $label = ': ' . $data['name'];
      } else {
        $label = ' [' . count($data) . ']';
      }

      if ($key === NULL) {
        return implode('', array_map(fn($k, $v) => $this->arrayToHtml($v, $k), array_keys($data), array_values($data)));
      }
      
      return '<div class="tree-row"><details class="debug-tree-item"><summary>' .
             '<span class="key">' . htmlspecialchars($key) . 
             '<span class="array-info">' . htmlspecialchars($label) . '</span>' .
             '</span></summary><div class="ml-4">' . 
             implode('', array_map(fn($k, $v) => $this->arrayToHtml($v, $k), array_keys($data), array_values($data))) . 
             '</div></details></div>';
    }
    
    if (is_string($data) && mb_strlen($data) > 30) {
      return '<div class="tree-row"><details class="debug-tree-item"><summary>' .
             '<span class="key">' . htmlspecialchars($key) . ':</span> ' .
             '<span class="value">' . htmlspecialchars(mb_substr($data, 0, 30)) . '...</span>' .
             '</summary><div class="ml-4"><span class="value">' . 
             htmlspecialchars($data) . '</span></div></details></div>';
    }
    
    return '<div class="tree-row">' .
           '<span class="tree-item"><span class="expander-placeholder"></span>' .
           '<span class="key">' . htmlspecialchars($key) . ':</span> ' .
           '<span class="value">' . htmlspecialchars(json_encode($data, JSON_UNESCAPED_SLASHES)) . 
           '</span></span></div>';
  }
}
