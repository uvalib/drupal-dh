#!/bin/bash

# Don't use set -e as we want to handle errors ourselves
set +e

# Function to check if module is enabled
is_module_enabled() {
    ddev drush pm-list --status=enabled --type=module --format=list 2>/dev/null | grep -q "^$1$"
}

echo "Checking module status..."
if is_module_enabled "dh_dashboard"; then
    echo "Uninstalling DH Dashboard module..."
    ddev drush pm:uninstall dh_dashboard -y
fi

echo "Ensuring required modules are enabled..."
ddev drush pm:enable node layout_builder layout_discovery -y

echo "Cleaning up any existing configuration..."
# Delete specific configs we know might exist
ddev drush config-delete core.entity_view_display.node.student_dashboard.default -y 2>/dev/null || true
ddev drush config-delete node.type.student_dashboard -y 2>/dev/null || true

echo "Installing DH Dashboard module..."
ddev drush pm:enable dh_dashboard -y

echo "Granting dashboard access to authenticated users..."
ddev drush role:perm:add authenticated "access dh dashboard"

echo "Verifying permissions (checking authenticated role)..."
ddev drush role-list | grep -A 20 "authenticated:"

echo "Rebuilding cache..."
ddev drush cache:rebuild

echo "Installation completed. Check the status with: ddev drush pm:list | grep dashboard"
