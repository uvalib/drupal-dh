#!/bin/bash
set +e

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

debug() {
    echo "[DEBUG] $1" >&2
}

# Check DDEV environment
if ! command -v ddev >/dev/null 2>&1; then
    log "ERROR: Must be run within DDEV"
    exit 1
fi

log "Starting certificate module reinstallation..."

# First check if module is installed
MODULE_STATUS=$(ddev drush pm-list --type=module --status=enabled --format=list | grep -c "^dh_certificate$" || true)

if [ "$MODULE_STATUS" -eq 1 ]; then
    log "Uninstalling existing dh_certificate module..."
    
    debug "Current module status:"
    ddev drush pml | grep dh_certificate || true
    
    debug "Database tables before cleanup:"
    ddev mysql -e "SHOW TABLES LIKE '%course%';"

    # First remove from core.extension and modules
    debug "Removing module from system..."
    ddev mysql -e "DELETE FROM key_value WHERE collection='system.schema' AND name='dh_certificate';
                  DELETE FROM config WHERE name='core.extension';
                  DELETE FROM key_value WHERE collection='extension.list.module' AND name='dh_certificate';
                  DELETE FROM config WHERE name LIKE 'dh_certificate.%';"

    debug "Cleaning up database..."
    ddev mysql -e "SET FOREIGN_KEY_CHECKS=0;
                   DELETE FROM key_value WHERE collection='entity.definitions.installed';
                   DROP TABLE IF EXISTS course_enrollment;
                   DROP TABLE IF EXISTS dh_certificate_enrollments;
                   DROP TABLE IF EXISTS dh_certificate_progress;
                   DROP TABLE IF EXISTS student_progress;
                   DROP TABLE IF EXISTS dh_certificate_progress__completed_courses;
                   DELETE FROM cache_discovery WHERE cid LIKE '%dh_certificate%';
                   DELETE FROM cache_config WHERE cid LIKE '%dh_certificate%';
                   DELETE FROM cache_default WHERE cid LIKE '%dh_certificate%';
                   SET FOREIGN_KEY_CHECKS=1;"

    # Force rebuild caches
    debug "Rebuilding caches..."
    ddev drush cr

    # Verify module is gone (fix syntax error)
    VERIFY_STATUS=$(ddev drush pm-list --type=module --status=enabled --format=list | grep -c "^dh_certificate$" || true)
    if [ "$VERIFY_STATUS" -eq 1 ]; then
        log "ERROR: Module is still installed after uninstall attempt"
        exit 1
    fi
    
    log "Module successfully uninstalled"
fi

# Clean up any remaining config
log "Cleaning configurations..."
configs=(
    "block.block.certificate_progress"
    "user.role.dhcert_admin"
    "dh_certificate.settings"
    "dh_certificate.requirements"
    "dh_certificate.requirement_set.*"
    "field.storage.user.field_certificate_progress"
    "field.field.user.user.field_certificate_progress"
    "views.view.certificate_progress"
)

for config in "${configs[@]}"; do
    log "Removing config: $config"
    ddev drush config:delete -y "$config" >/dev/null 2>&1 || true
done

# Clean up roles
log "Cleaning up roles..."
ddev drush role:delete dhcert_admin >/dev/null 2>&1 || true

# Install module
log "Installing module..."
if ! ddev drush -y pm:enable dh_certificate; then
    log "ERROR: Module installation failed"
    exit 1
fi

# Generate test content if requested
if [ "${1:-}" = "--with-samples" ]; then
    log "Generating sample content..."
    ddev drush dh-certificate:generate-test || log "Warning: Sample generation failed"
fi

# Only rebuild cache once at the end
log "Rebuilding cache..."
ddev drush cr

log "Installation complete"
