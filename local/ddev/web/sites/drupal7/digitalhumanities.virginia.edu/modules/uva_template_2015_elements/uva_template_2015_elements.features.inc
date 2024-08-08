<?php
/**
 * @file
 * uva_template_2015_elements.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function uva_template_2015_elements_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "ds" && $api == "ds") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_views_api().
 */
function uva_template_2015_elements_views_api($module = NULL, $api = NULL) {
  return array("api" => "3.0");
}

/**
 * Implements hook_image_default_styles().
 */
function uva_template_2015_elements_image_default_styles() {
  $styles = array();

  // Exported image style: article_image.
  $styles['article_image'] = array(
    'label' => 'article large image',
    'effects' => array(
      2 => array(
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => 500,
          'height' => 333,
        ),
        'weight' => 1,
      ),
    ),
  );

  // Exported image style: article_news_full_image.
  $styles['article_news_full_image'] = array(
    'label' => 'article & news full image 853x570',
    'effects' => array(
      16 => array(
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => 853,
          'height' => 570,
        ),
        'weight' => 1,
      ),
    ),
  );

  // Exported image style: article_small_image.
  $styles['article_small_image'] = array(
    'label' => 'article small image',
    'effects' => array(
      3 => array(
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => 309,
          'height' => 208,
        ),
        'weight' => 1,
      ),
    ),
  );

  // Exported image style: homepage_image.
  $styles['homepage_image'] = array(
    'label' => 'Homepage image',
    'effects' => array(
      1 => array(
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => 2000,
          'height' => 800,
        ),
        'weight' => 1,
      ),
    ),
  );

  // Exported image style: homepage_news_image.
  $styles['homepage_news_image'] = array(
    'label' => 'Homepage News image',
    'effects' => array(
      11 => array(
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => 340,
          'height' => 227,
        ),
        'weight' => 1,
      ),
    ),
  );

  return $styles;
}

/**
 * Implements hook_node_info().
 */
function uva_template_2015_elements_node_info() {
  $items = array(
    'news' => array(
      'name' => t('News'),
      'base' => 'node_content',
      'description' => t('Use this to add news items to the homepage.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'page' => array(
      'name' => t('Basic page'),
      'base' => 'node_content',
      'description' => t('Use <em>basic pages</em> for your static content, such as an \'About us\' page.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'section_links' => array(
      'name' => t('Section Links'),
      'base' => 'node_content',
      'description' => t('Use this to add links to the Section Links area on the homepage.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'slideshow' => array(
      'name' => t('Homepage Slideshow'),
      'base' => 'node_content',
      'description' => t('Use this to upload a new image and/or caption to the homepage slideshow.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
  );
  drupal_alter('node_info', $items);
  return $items;
}
