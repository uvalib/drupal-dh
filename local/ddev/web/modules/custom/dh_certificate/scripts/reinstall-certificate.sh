#!/bin/bash
set -e

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Check if module is installed
is_module_installed() {
    ddev exec drush pm-list --type=module --status=enabled --format=list | grep -q "^$1$"
}

# Verify ddev exists
if ! command -v ddev &> /dev/null; then
    log "ERROR: ddev command not found"
    exit 1
fi

# Check if we're in a ddev project
if ! ddev describe >/dev/null 2>&1; then
    log "ERROR: Not in a ddev project directory"
    exit 1
fi

log "Starting certificate module reinstallation..."

# Clean up configuration first
log "Removing existing configuration..."
CONFIG_ITEMS=(
    "block.block.certificate_progress"
    "user.role.dhcert_admin"
    "core.entity_form_display.node.certificate.default"
    "core.entity_view_display.node.certificate.default"
    "core.entity_view_display.node.certificate.teaser"
)

for CONFIG in "${CONFIG_ITEMS[@]}"; do
    if ddev drush config-get "$CONFIG" >/dev/null 2>&1; then
        ddev drush config-delete "$CONFIG" -y >/dev/null 2>&1
    fi
done

# Delete all requirement-related config quietly
log "Removing requirement configurations..."
ddev drush config-delete requirement_set 2>/dev/null || true
ddev drush config-delete requirement_type 2>/dev/null || true
ddev drush config-delete requirement_type_template 2>/dev/null || true

# Delete requirement entities in the correct order
log "Removing requirement entities and related content..."
ENTITY_TYPES=(
    "requirement_set"
    "requirement"
    "requirement_type"
    "requirement_type_template"
    "course_enrollment"
)

for ENTITY_TYPE in "${ENTITY_TYPES[@]}"; do
    log "Deleting $ENTITY_TYPE entities..."
    # Use config-delete for config entities and entity:delete for content entities
    if [[ "$ENTITY_TYPE" == *"requirement_type"* ]]; then
        ddev exec "drush config-delete $ENTITY_TYPE.*" || log "No $ENTITY_TYPE configs to delete"
    else
        ddev exec "drush entity:delete $ENTITY_TYPE" || log "No $ENTITY_TYPE entities to delete"
    fi
done

# Clear cache
log "Clearing cache..."
ddev exec drush cr

# Uninstall module if installed
if is_module_installed "dh_certificate"; then
    log "Uninstalling dh_certificate module..."
    ddev exec drush pm:uninstall -y dh_certificate
fi

# Clear cache again
ddev exec drush cr

# Install module
log "Installing certificate module..."
if ! ddev exec drush pm:enable -y dh_certificate; then
    log "ERROR: Failed to install dh_certificate module"
    exit 1
fi

# Run setup command
log "Running initial setup..."
ddev exec drush dhc-setup-all --reset --uid=1

log "Reinstallation complete!"
