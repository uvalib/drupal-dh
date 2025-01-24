<?php
$databases['migrate']['default'] = array (
  'database' => 'drupal7migrate',
  'username' => 'db',
  'password' => 'db',
  'prefix' => '',
  'host' => 'db',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$settings['devops_label']='ddev local';
$settings['twig_debug'] = TRUE;

// Replace cache.backend.null with cache.backend.memory
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';

// Additional development settings
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['extension_discovery_scan_tests'] = TRUE;
$settings['rebuild_access'] = TRUE;
$settings['skip_permissions_hardening'] = TRUE;
