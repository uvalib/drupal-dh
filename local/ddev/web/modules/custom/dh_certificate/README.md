# Digital Humanities Certificate

A Drupal module for managing digital humanities certificate programs, requirements, and student progress.

## Quick Start

```bash
# Install the module
ddev composer require drupal/dh_certificate
drush en dh_certificate

# Generate example data
drush dhc-gen-ex --reset
drush dhc-gen-test --uid=1

# Check progress
drush dhc-progress 1
```

## Documentation

Detailed documentation is available in the `doc` directory:

### Core Concepts
- [Requirements System](doc/requirements.md)
- [Requirement Type Templates](doc/requirement-templates.md)
- [Progress Tracking](doc/progress.md)

### Reference
- [Drush Commands](doc/drush.md)
- [Development Guide](doc/development.md)

## Administration

Access the administration interface at:
- `/admin/config/dh-certificate`
- `/admin/dh-certificate/progress`
- `/admin/dh-certificate/requirements`

## Contributing

1. Follow Drupal coding standards
2. Add tests for new features
3. Update documentation as needed
4. Submit pull requests against develop branch
