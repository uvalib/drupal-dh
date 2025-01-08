#!/bin/bash -x

# Initial cleanup
ddev drush cr
ddev drush pmu dh_dashboard -y || true

# Ensure required modules are enabled first
ddev drush en node text field -y

# Explicitly create body field storage
ddev drush php:eval '
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

// Delete existing field storage if it exists
$storage = \Drupal::entityTypeManager()->getStorage("field_storage_config");
if ($existing = $storage->load("node.body")) {
    $existing->delete();
}

// Create new field storage
FieldStorageConfig::create([
    "entity_type" => "node",
    "field_name" => "body",
    "type" => "text_with_summary",
    "module" => "text",
    "cardinality" => 1,
    "translatable" => TRUE,
])->save();
'

# Clear caches after field storage creation
ddev drush cr

# Now proceed with module installation
ddev drush en dh_dashboard -y || true

# Final cache rebuild
ddev drush cr

# Verify field storage exists
ddev drush php:eval 'var_dump(\Drupal::entityTypeManager()->getStorage("field_storage_config")->load("node.body") ? "Body field storage exists" : "Body field storage MISSING");'
