#!/bin/bash
set -e

# Add all DH Certificate permissions to administrator role
ddev drush role:permission:add administrator "administer dh certificate"
ddev drush role:permission:add administrator "administer dh certificate settings"
ddev drush role:permission:add administrator "administer dh certificate requirements"
ddev drush role:permission:add administrator "administer dh certificate courses"
ddev drush role:permission:add administrator "view certificate progress"
ddev drush role:permission:add administrator "view own certificate progress"

# Clear caches to ensure permissions take effect
ddev drush cr

echo "Administrator permissions have been updated."