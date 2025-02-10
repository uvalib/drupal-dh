# Technical Guide

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

## Core Components

### Requirement Templates
Templates define reusable requirement configurations through config entities. These 
are managed via the UI or Drush commands.

```bash
# List templates
drush dhc-lt

# Generate examples
drush dhc-gen-ex

# Create template
drush dhc-ct [id] [label] [type] --config='[json]'
```

### Progress Tracking
Progress is tracked through the ProgressManager service:

```php
// Get progress for user
$progress = \Drupal::service('dh_certificate.progress')
  ->getUserProgress($account);
```

### Structure Monitoring 
Changes to course and requirement structures are monitored and logged:

```bash
# Check structure
drush dhc-monitor

# Record current state
drush dhc-monitor-record
```

## Entity Hierarchy

1. **Certificate**
   - Top-level entity representing a complete certificate program
   - Contains multiple Requirement Sets
   - Examples: "Digital Humanities Certificate", "Data Science Certificate"

2. **Requirement Set**
   - Grouping of related requirements
   - Belongs to a Certificate
   - Examples: "Core Courses", "Electives", "Technical Skills"

3. **Requirement Type**
   - Defines the nature of requirements
   - Used to categorize and validate requirements
   - Examples: "Course", "Skill", "Project"

4. **Requirement**
   - Individual items that must be completed
   - Belongs to a Requirement Set
   - Has a specific Requirement Type
   - Examples: "DH101", "Programming Skills", "Capstone Project"

## Relationships

## Documentation

Detailed documentation is available in the `doc` directory:

### Core Concepts
- [Requirements System](doc/requirements.md)
- [Requirement Type Templates](doc/requirement-templates.md)
- [Progress Tracking](doc/progress.md)

### Reference
- [Drush Commands](doc/drush.md)
- [Development Guide](doc/development.md)

## Development

### Key Files
- `src/Entity/RequirementTypeTemplate.php` - Template entity definition
- `src/ListBuilder/RequirementTypeTemplateListBuilder.php` - Template UI handler
- `src/Progress/ProgressManager.php` - Progress tracking service

### Adding Requirements
1. Create template configuration
2. Define validation rules
3. Add tracking logic
4. Update progress calculations

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

## See Also
- [Requirements Guide](doc/Requirements.md)
- [Template Reference](doc/requirement-templates.md)

## Development Tools

### Commands
```