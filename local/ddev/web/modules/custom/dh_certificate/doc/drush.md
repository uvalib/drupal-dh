# DH Certificate Drush Commands

## Quick Start

```bash
# Complete setup with example data
drush dhc-setup-all --reset --uid=12

# Check progress
drush dhc-progress 12

# List enrollments
drush dhc-list-enroll
```

## Setup Commands

### Complete Setup
```bash
# Full setup with example data
drush dhc-setup-all [--reset] [--uid=12]
  --reset   : Clear existing data before setup
  --uid     : Target user ID (default: 1)
```

### Component Setup
```bash
# Generate templates
drush dhc-gen-templates [--reset]

# Generate requirement sets
drush dhc-gen-req [--reset]

# Generate standard requirements
drush dhc-gen-std-req [--reset]
```

## Management Commands

### Templates
```bash
# List templates
drush dhc-lt

# Create template
drush dhc-ct [id] [label] [type] --config='{"key":"value"}'
Example: drush dhc-ct core_methods "Core Methods" course --config='{"credits":3}'

# Delete template
drush dhc-dt [id]
```

### Progress & Enrollments
```bash
# Check user progress
drush dhc-progress [uid]

# Generate enrollments
drush dhc-gen-enroll [user_ids] [--count=5] [--retain]

# List enrollments
drush dhc-list-enroll
```

### Data Management
```bash
# Reset everything
drush dh-reset

# Generate fresh data
drush dhc-setup-all --reset --uid=12
```

## Options Reference

Common options available:
- `--reset`: Clear existing data
- `--uid=N`: Target user ID
- `--retain`: Keep existing enrollments
- `--config='{"key":"value"}'`: Template configuration

## See Also

- [Requirement Templates](requirement-templates.md)
- [Progress Tracking](progress.md)
- [Requirements Overview](Requirements.md)
