
# DH Certificate Documentation

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

## Reference

- [Drush Commands](drush.md)
  - All available commands
  - Examples and options
  - Common workflows

- [Development Guide](development.md)
  - Architecture overview
  - Service descriptions
  - Testing guidelines

## Quick Links

### Administration
- `/admin/config/dh-certificate`
- `/admin/dh-certificate/progress`
- `/admin/dh-certificate/requirements`

### Common Commands
```bash
# Generate examples
drush dhc-gen-ex --reset

# Check progress
drush dhc-progress [uid]

# Debug system
drush dhc-debug
```