# DH Certificate Documentation

## Getting Started

```bash
# Complete setup with example data
drush dhc-setup-all --reset --uid=12

# Check progress
drush dhc-progress 12

# List enrollments
drush dhc-list-enroll
```

## Core Documentation

- [Requirements System](requirements.md)
- [Requirement Templates](requirement-templates.md)
- [Progress Tracking](progress.md)

## System Management

### Administrative Tasks
```bash
# Full system setup
drush dhc-setup-all --reset --uid=12

# Generate templates
drush dhc-gen-templates --reset

# Generate requirements
drush dhc-gen-req --reset
```

### Monitoring Commands
```bash
# List templates
drush dhc-lt

# Check progress
drush dhc-progress [uid]

# List enrollments
drush dhc-list-enroll
```

# Debug system
drush dhc-debug
```

## Administration Interface

### Configuration
- `/admin/config/dh-certificate/settings` - System settings
- `/admin/config/dh-certificate/requirement-templates` - Template management
- `/admin/config/dh-certificate/requirement-sets` - Set configuration

### Monitoring
- `/admin/dh-certificate/progress` - Progress overview
- `/admin/dh-certificate/requirements` - Requirement status
- `/admin/dh-certificate/debug` - System diagnostics

## See Also

- [Development Guide](development.md)
- [API Documentation](api.md)
- [Troubleshooting Guide](troubleshooting.md)