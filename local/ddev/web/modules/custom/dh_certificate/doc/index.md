# DH Certificate Documentation

## Getting Started

- [Quick Setup Guide](requirements.md#quick-setup)
  - Installation and basic configuration
  - Example data generation
  - Initial system setup

## Core Documentation

- [Requirements System](requirements.md)
  - Core concepts and relationships
  - Safe management practices
  - Best practices and warnings

- [Requirement Templates](requirement-templates.md)
  - Template types and examples
  - Configuration options
  - Management commands

- [Progress Tracking](progress.md)
  - Progress states and calculation
  - Monitoring and reporting
  - Troubleshooting

## System Management

### Administrative Tasks
```bash
# Full system setup
drush dhc-setup --reset --uid=12

# Generate fresh templates
drush dhc-templates --reset

# Generate requirement sets
drush dhc-gen-req --reset
```

### Monitoring Commands
```bash
# List templates
drush dhc-lt

# Check progress
drush dhc-progress [uid]

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