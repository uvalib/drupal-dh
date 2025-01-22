#!/bin/bash

set -e

echo "Starting DH Dashboard reinstallation process..."

# Delete any existing dashboard nodes first
echo "Checking for existing dashboard nodes..."
node_count=$(ddev drush eval "print count(\\Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'dh_dashboard']))")
if [ "$node_count" -gt 0 ]; then
  echo "Deleting $node_count dashboard nodes..."
  ddev drush eval "\\Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'dh_dashboard'])" | xargs -I {} ddev drush entity:delete node {}
fi

# Delete existing configuration
echo "Checking existing configurations..."
ddev drush eval '
$config_factory = \Drupal::configFactory();

$configs = [
  "dh_dashboard.settings",
  "node.type.dh_dashboard",
  "block.block.dh_dashboard_news",
  "block.block.dh_dashboard_events",
  "block.block.dh_dashboard_progress",
  "block.block.dh_dashboard_certificate_info",
  "views.view.dh_dashboard_news",
  "views.view.dh_dashboard_events"
];

foreach ($configs as $config_name) {
  if ($config_factory->get($config_name)->get()) {
    echo "Deleting config: $config_name\n";
    $config_factory->getEditable($config_name)->delete();
  }
}
'

# Check if module exists and is not already disabled before uninstalling
echo "Checking if DH Dashboard module is installed..."
if ddev drush pm:list --field=name | grep -q "^dh_dashboard$"; then
  if ! ddev drush pm:list --status=disabled --field=name | grep -q "^dh_dashboard$"; then
    echo "Uninstalling DH Dashboard module..."
    ddev drush pm:uninstall dh_dashboard -y
  else
    echo "DH Dashboard module is already disabled."
  fi
else
  echo "DH Dashboard module not currently installed."
fi

# Check if module is already enabled before enabling
echo "Checking DH Dashboard module status..."
if ddev drush pm:list --status=enabled --field=name | grep -q "^dh_dashboard$"; then
  echo "DH Dashboard module is already enabled."
else
  echo "Enabling DH Dashboard module..."
  ddev drush pm:enable dh_dashboard -y
fi

# Clear all caches last
echo "Rebuilding caches..."
ddev drush cr

echo "Done!"
