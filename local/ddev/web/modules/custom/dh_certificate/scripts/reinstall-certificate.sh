#!/bin/bash
set +e

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Check DDEV environment
if ! command -v ddev >/dev/null 2>&1; then
    log "ERROR: Must be run within DDEV"
    exit 1
fi

log "Starting certificate module reinstallation..."

# Ensure clean state
log "Flushing all caches..."
ddev drush cr

# Uninstall and clean up
log "Disabling module and cleaning up..."
ddev drush pm:uninstall dh_certificate -y >/dev/null 2>&1 || true

# Clean database
log "Cleaning database..."
ddev mysql -e "SET FOREIGN_KEY_CHECKS=0;
               DROP TABLE IF EXISTS course_enrollment;
               DROP TABLE IF EXISTS dh_certificate_enrollments;
               DROP TABLE IF EXISTS dh_certificate_progress;
               DROP TABLE IF EXISTS student_progress;
               SET FOREIGN_KEY_CHECKS=1;"

# Clear configurations - expanded list
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

# Clear all caches before install
log "Clearing caches..."
ddev drush cr

# Install module
log "Installing module..."
ddev drush pm:enable -y dh_certificate || {
    log "ERROR: Module installation failed"
    exit 1
}

# Generate test content if requested
if [ "${1:-}" = "--with-samples" ]; then
    log "Generating sample content..."
    ddev drush dh-certificate:generate-test || log "Warning: Sample generation failed"
fi

ddev drush cr
log "Installation complete"
