#!/bin/bash

set -e

echo "Reinstalling DH Certificate module..."

# Check if certificate module exists and is not already disabled before uninstalling
echo "Checking if certificate module is installed..."
if ddev drush pm:list --field=name | grep -q "^dh_certificate$"; then
  if ! ddev drush pm:list --status=disabled --field=name | grep -q "^dh_certificate$"; then
    # Clean up enrollments first
    echo "Cleaning up any existing enrollments..."
    ddev drush dhc-clean-enroll

    echo "Uninstalling certificate module..."
    ddev drush pm:uninstall dh_certificate -y
  else
    echo "Certificate module is already disabled."
  fi
else
  echo "Certificate module not currently installed."
fi

# Clean up certificate configurations
echo "Checking certificate configurations..."
ddev drush eval '
$config_factory = \Drupal::configFactory();
$configs = [
  "dh_certificate.settings",
  "dh_certificate.requirements",
  "dh_certificate.requirement_types",
  "field.storage.user.field_certificate_progress",
  "field.field.user.user.field_certificate_progress",
  "block.block.certificate_progress",
  "views.view.certificate_progress",
  "node.type.dh_course"
];

foreach ($configs as $config_name) {
  if ($config_factory->get($config_name)->get()) {
    echo "Deleting config: $config_name\n";
    $config_factory->getEditable($config_name)->delete();
  }
}
'

# Check if certificate module is already enabled before enabling
echo "Checking certificate module status..."
if ddev drush pm:list --status=enabled --field=name | grep -q "^dh_certificate$"; then
  echo "Certificate module is already enabled."
else
  echo "Enabling certificate module..."
  ddev drush pm:enable dh_certificate -y
fi

# Check if dashboard module exists and is not already disabled before uninstalling
echo "Checking if dashboard module is installed..."
if ddev drush pm:list --field=name | grep -q "^dh_dashboard$"; then
  if ! ddev drush pm:list --status=disabled --field=name | grep -q "^dh_dashboard$"; then
    echo "Uninstalling dashboard module..."
    ddev drush pm:uninstall dh_dashboard -y
  else
    echo "Dashboard module is already disabled."
  fi
else
  echo "Dashboard module not currently installed."
fi

# Check if dashboard module is already enabled before enabling
echo "Checking dashboard module status..."
if ddev drush pm:list --status=enabled --field=name | grep -q "^dh_dashboard$"; then
  echo "Dashboard module is already enabled."
else
  echo "Enabling dashboard module..."
  ddev drush pm:enable dh_dashboard -v -y
fi

# Create sample courses if requested
if [ "${1:-}" = "--with-samples" ]; then
  echo "Creating sample courses..."
  ddev drush dh-certificate:generate-test
fi

# Clear caches last
echo "Rebuilding caches..."
ddev drush cr

echo "Installation complete!"
