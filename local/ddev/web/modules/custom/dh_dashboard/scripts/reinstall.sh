#!/bin/bash

set -e

echo "Starting DH Dashboard reinstallation process..."

# Clear all caches first
echo "Clearing all caches..."
ddev drush cr

# Delete any existing dashboard nodes
echo "Deleting existing dashboard nodes..."
ddev drush eval "\\Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'dh_dashboard'])" | xargs -I {} ddev drush entity:delete node {} || true

# Uninstall our module only
echo "Uninstalling DH Dashboard module..."
ddev drush pm:uninstall dh_dashboard -y || true

# Re-enable our module
echo "Re-enabling DH Dashboard module..."
ddev drush pm:enable dh_dashboard -y

# Final cache rebuild
echo "Final cache rebuild..."
ddev drush cr

echo "Done!"
