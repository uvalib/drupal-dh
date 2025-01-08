#!/bin/bash -x

# Check field storage status
echo "Checking field storage status..."
ddev drush php:eval '
$storage = \Drupal::entityTypeManager()->getStorage("field_storage_config");
$body = $storage->load("node.body");
var_dump($body ? "Body field storage exists" : "Body field storage MISSING");
'

# List all field storages
echo "Listing all field storages..."
ddev drush php:eval '
$storage = \Drupal::entityTypeManager()->getStorage("field_storage_config");
$fields = $storage->loadMultiple();
foreach ($fields as $field) {
  echo $field->id() . "\n";
}
'

# Check module dependencies
echo "Checking module status..."
ddev drush pm-list --type=module --status=enabled --format=list | grep -E "text|field|node"
