#!/bin/bash
set -e

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Check if module is installed
is_module_installed() {
    ddev drush pm-list --type=module --status=enabled --format=list | grep -q "^$1$"
}

# Check DDEV environment
if ! command -v ddev >/dev/null 2>&1; then
    log "ERROR: Must be run within DDEV"
    exit 1
fi

log "Starting certificate module reinstallation..."

declare -a configs=(
    "block.block.certificate_progress"
    "user.role.dhcert_admin"
    "core.entity_form_display.node.certificate.default"
    "core.entity_view_display.node.certificate.default"
    "core.entity_view_display.node.certificate.teaser"
    "field.field.node.certificate.field_certificate_description"
    "node.type.certificate"
    "field.storage.node.field_certificate_description"
    "views.view.certificates"
)

# Delete configs and uninstall module if installed
for config in "${configs[@]}"; do
    ddev drush sql:query "DELETE FROM config WHERE name='$config';" 
done

# Check if entity type exists
ENTITY_NAME="course_enrollment"

ddev drush entity:delete $ENTITY_NAME || echo "continuing"

ddev drush cr 

if is_module_installed "dh_certificate"; then
    ddev drush pm:uninstall -y dh_certificate 
fi

ddev drush cr 

# Install module
log "Installing certificate module..."
if ! ddev drush pm:enable -y dh_certificate; then
    log "ERROR: Failed to install dh_certificate module"
    exit 1
fi

log "Installation complete"
