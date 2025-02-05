# DH Certificate Drush Commands

## Setup Commands

### Complete Setup
```bash
# Full setup with clean slate
drush dhc-setup --reset --uid=12

# Add to existing setup
drush dhc-setup --uid=12
```

### Component Setup
```bash
# Templates
drush dhc-templates [--reset]

# Requirement Sets
drush dhc-gen-req [--reset]

# Standard Requirements
drush dhc-gen-std-req [--reset]
```

## Management Commands

### Templates
```bash
# List templates
drush dhc-lt

# Create template
drush dhc-ct [id] [label] [type]

# Delete template
drush dhc-dt [id]
```

### Enrollments
```bash
# List all enrollments
drush dhc-list-enroll

# Generate enrollments
drush dhc-gen-enroll [user_ids]

# Clean up enrollments
drush dhc-clean-enroll
```

### Progress
```bash
# Check user progress
drush dhc-progress [uid]

# Clean up progress data
drush dhc-clean-progress
```

### Debug
```bash
# Show debug info
drush dhc-debug

# Reset everything
drush dh-reset
```

## Common Workflows

### Fresh Start
```bash
# 1. Reset everything
drush dh-reset

# 2. Full setup
drush dhc-setup --reset --uid=12

# 3. Verify
drush dhc-debug
```

### Add New User
```bash
# 1. Generate enrollments
drush dhc-gen-enroll 15

# 2. Check progress
drush dhc-progress 15
```

### Troubleshooting
```bash
# 1. Check setup
drush dhc-debug

# 2. Clean problematic data
drush dhc-clean-progress
drush dhc-clean-enroll

# 3. Regenerate
drush dhc-setup --reset --uid=12
```
