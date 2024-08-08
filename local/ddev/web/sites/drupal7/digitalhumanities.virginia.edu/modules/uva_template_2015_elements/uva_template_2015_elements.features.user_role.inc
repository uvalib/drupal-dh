<?php
/**
 * @file
 * uva_template_2015_elements.features.user_role.inc
 */

/**
 * Implements hook_user_default_roles().
 */
function uva_template_2015_elements_user_default_roles() {
  $roles = array();

  // Exported role: administrator.
  $roles['administrator'] = array(
    'name' => 'administrator',
    'weight' => 2,
  );

  return $roles;
}
