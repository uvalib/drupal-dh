<?php
/**
 * @file
 * uva_template_2015_elements.features.filter.inc
 */

/**
 * Implements hook_filter_default_formats().
 */
function uva_template_2015_elements_filter_default_formats() {
  $formats = array();

  // Exported format: Filtered HTML.
  $formats['filtered_html'] = array(
    'format' => 'filtered_html',
    'name' => 'Filtered HTML',
    'cache' => 0,
    'status' => 1,
    'weight' => 0,
    'filters' => array(
      'filter_url' => array(
        'weight' => 0,
        'status' => 1,
        'settings' => array(
          'filter_url_length' => 72,
        ),
      ),
      'filter_tokens' => array(
        'weight' => 0,
        'status' => 1,
        'settings' => array(),
      ),
      'filter_html' => array(
        'weight' => 1,
        'status' => 1,
        'settings' => array(
          'allowed_html' => '<a> <em> <img> <strong> <embed> <object> <param> <ul> <ol> <li> <dl> <dt> <dd> <br> <br /> <h1> <h2> <h3> <h4> <h5> <h6> <blockquote> <hr> <hr /> <p> <span> <table> <tbody> <tr> <td> <th> <label> <select> <option> <iframe> <div> <strike> <caption> <area> <map> <sub> <sup>',
          'filter_html_help' => 1,
          'filter_html_nofollow' => 0,
        ),
      ),
      'filter_autop' => array(
        'weight' => 2,
        'status' => 1,
        'settings' => array(),
      ),
      'filter_htmlcorrector' => array(
        'weight' => 10,
        'status' => 1,
        'settings' => array(),
      ),
    ),
  );

  return $formats;
}
