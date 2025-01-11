<?php
/**
 * @file
 * uva_template_2015_elements.features.fe_block_settings.inc
 */

/**
 * Implements hook_default_fe_block_settings().
 */
function uva_template_2015_elements_default_fe_block_settings() {
  $export = array();

  $export['version'] = '2.0';

  $export['block-calltoaction'] = array(
    'cache' => -1,
    'css_class' => 'call-to-action',
    'custom' => 0,
    'machine_name' => 'calltoaction',
    'module' => 'block',
    'node_types' => array(),
    'pages' => '<front>',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'postscript_first',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => -14,
      ),
    ),
    'title' => '',
    'visibility' => 0,
  );

  $export['block-footer_contact'] = array(
    'cache' => -1,
    'css_class' => '',
    'custom' => 0,
    'machine_name' => 'footer_contact',
    'module' => 'block',
    'node_types' => array(),
    'pages' => '',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'footer_second',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => 0,
      ),
    ),
    'title' => '',
    'visibility' => 0,
  );

  $export['block-search_toggle'] = array(
    'cache' => -1,
    'css_class' => '',
    'custom' => 0,
    'machine_name' => 'search_toggle',
    'module' => 'block',
    'node_types' => array(),
    'pages' => '',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'user_second',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => 0,
      ),
    ),
    'title' => '',
    'visibility' => 0,
  );

  $export['block-social_media'] = array(
    'cache' => -1,
    'css_class' => 'social-media',
    'custom' => 0,
    'machine_name' => 'social_media',
    'module' => 'block',
    'node_types' => array(),
    'pages' => '',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'footer_fourth',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => 0,
      ),
    ),
    'title' => '',
    'visibility' => 0,
  );

  $export['block-uvabar_logo'] = array(
    'cache' => -1,
    'css_class' => 'uvabar_logo',
    'custom' => 0,
    'machine_name' => 'uvabar_logo',
    'module' => 'block',
    'node_types' => array(),
    'pages' => '',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'user_first',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => 0,
      ),
    ),
    'title' => '',
    'visibility' => 0,
  );

  $export['menu_block-1'] = array(
    'cache' => -1,
    'css_class' => '',
    'custom' => 0,
    'delta' => 1,
    'module' => 'menu_block',
    'node_types' => array(),
    'pages' => '<front>',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'sidebar_first',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => 0,
      ),
    ),
    'title' => 'Submenu Title',
    'visibility' => 0,
  );

  $export['nice_menus-1'] = array(
    'cache' => -1,
    'css_class' => '',
    'custom' => 0,
    'delta' => 1,
    'module' => 'nice_menus',
    'node_types' => array(),
    'pages' => '',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'menu',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => -15,
      ),
    ),
    'title' => '<none>',
    'visibility' => 0,
  );

  $export['search-form'] = array(
    'cache' => -1,
    'css_class' => '',
    'custom' => 0,
    'delta' => 'form',
    'module' => 'search',
    'node_types' => array(),
    'pages' => '',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => 'content',
        'status' => 1,
        'theme' => 'omega',
        'weight' => -1,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 1,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'top',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => -15,
      ),
    ),
    'title' => '',
    'visibility' => 0,
  );

  $export['system-main'] = array(
    'cache' => -1,
    'css_class' => '',
    'custom' => 0,
    'delta' => 'main',
    'module' => 'system',
    'node_types' => array(),
    'pages' => '',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => 'content',
        'status' => 1,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => 'content',
        'status' => 1,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'content',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => -17,
      ),
    ),
    'title' => '',
    'visibility' => 0,
  );

  $export['system-main-menu'] = array(
    'cache' => -1,
    'css_class' => '',
    'custom' => 0,
    'delta' => 'main-menu',
    'module' => 'system',
    'node_types' => array(),
    'pages' => '',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'uvatemplate2015_theme',
        'weight' => 0,
      ),
    ),
    'title' => '',
    'visibility' => 0,
  );

  $export['views-15a9856f2be6ff1757f4dd0ff397faa9'] = array(
    'cache' => -1,
    'css_class' => 'subpage-articles',
    'custom' => 0,
    'delta' => '15a9856f2be6ff1757f4dd0ff397faa9',
    'module' => 'views',
    'node_types' => array(),
    'pages' => '<front>',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'postscript_first',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => -15,
      ),
    ),
    'title' => 'More to Explore',
    'visibility' => 0,
  );

  $export['views-featured_articles-block'] = array(
    'cache' => -1,
    'css_class' => '',
    'custom' => 0,
    'delta' => 'featured_articles-block',
    'module' => 'views',
    'node_types' => array(),
    'pages' => '<front>',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'content',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => -12,
      ),
    ),
    'title' => '',
    'visibility' => 0,
  );

  $export['views-homepage_slideshow-block'] = array(
    'cache' => -1,
    'css_class' => '',
    'custom' => 0,
    'delta' => 'homepage_slideshow-block',
    'module' => 'views',
    'node_types' => array(),
    'pages' => '<front>',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'header_first',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => 0,
      ),
    ),
    'title' => '',
    'visibility' => 1,
  );

  $export['views-news-block'] = array(
    'cache' => -1,
    'css_class' => 'news',
    'custom' => 0,
    'delta' => 'news-block',
    'module' => 'views',
    'node_types' => array(),
    'pages' => '<front>',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'content',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => -16,
      ),
    ),
    'title' => 'In the News',
    'visibility' => 1,
  );

  $export['views-section_links-block'] = array(
    'cache' => -1,
    'css_class' => 'section-links',
    'custom' => 0,
    'delta' => 'section_links-block',
    'module' => 'views',
    'node_types' => array(),
    'pages' => '<front>',
    'roles' => array(),
    'themes' => array(
      'omega' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'omega',
        'weight' => 0,
      ),
      'seven' => array(
        'region' => '',
        'status' => 0,
        'theme' => 'seven',
        'weight' => 0,
      ),
      'uvatemplate2015_theme' => array(
        'region' => 'content',
        'status' => 1,
        'theme' => 'uvatemplate2015_theme',
        'weight' => -13,
      ),
    ),
    'title' => 'Related Links',
    'visibility' => 1,
  );

  return $export;
}
