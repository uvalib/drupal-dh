# Certificate Progress Tracking

## Overview

The DH Certificate progress tracking system monitors and manages student progress towards certificate completion. It tracks course enrollments, requirement completions, and overall certificate status.

## Quick Start

```bash
# Check progress for a user
drush dhc-progress 1

# Generate test data
drush dhc-gen-test --uid=1

# Debug progress
drush dhc-debug
```

## Progress Components

### 1. Course Progress
- Enrollment status (pending, in-progress, completed)
- Credit accumulation
- Core vs. elective completion
- Term/timeline tracking

### 2. Requirement Progress
- Core requirements status
- Elective requirements status
- Tool proficiency progress
- Project milestone completion

### 3. Overall Progress
- Total percentage complete
- Credits earned vs. required
- Remaining requirements
- Expected completion date

## Progress Tracking Commands

```bash
# Check specific user progress
drush dhc-progress [uid]

# Clean up progress data
drush dhc-clean-progress

# Debug enrollments
drush dhc-debug

# List all enrollments
drush dhc-list-enroll
```

## Progress States

1. **Not Started** (0%)
   - No enrollments
   - No requirements met

2. **In Progress** (1-99%)
   - Active enrollments
   - Some requirements met
   - Tracking towards completion

3. **Complete** (100%)
   - All requirements met
   - All credits earned
   - Ready for certification

## Progress Monitoring

### Dashboard View
```bash
drush dh-cp [uid]
```
Shows:
- Overall progress percentage
- Requirements status
- Recent activity
- Next steps

### Debug Information
```bash
drush dhc-debug
```
Shows:
- Enrollment records
- Completion dates
- Status changes
- Data integrity

## Administration

### User Interface
- Student Progress: `/admin/dh-certificate/progress`
- Progress Details: `/admin/dh-certificate/progress/{user}`
- Progress Settings: `/admin/config/dh-certificate/settings`

### Batch Operations
```bash
# Reset progress data
drush dh-reset

# Generate test data
drush dhc-gen-test --uid=[uid]

# Clean specific data
drush dhc-clean-progress
drush dhc-clean-enroll
```

## Progress Calculation

1. **Credit-based Progress**
   ```php
   $progress = (earned_credits / required_credits) * 100;
   ```

2. **Requirement-based Progress**
   ```php
   $progress = (completed_requirements / total_requirements) * 100;
   ```

3. **Weighted Progress**
   ```php
   $progress = ($core_progress * 0.6) + ($elective_progress * 0.4);
   ```

## Best Practices

1. Regular Progress Checks
   ```bash
   # Weekly progress check
   drush dhc-progress [uid]
   ```

2. Data Maintenance
   ```bash
   # Monthly cleanup
   drush dhc-clean-progress
   drush dhc-gen-test --reset
   ```

3. Progress Monitoring
   ```bash
   # Debug checks
   drush dhc-debug
   drush dh-cp [uid]
   ```

## Troubleshooting

1. Progress Not Updating
   ```bash
   # Clear progress cache
   drush cr
   drush dhc-clean-progress
   ```

2. Missing Enrollments
   ```bash
   # Check enrollment data
   drush dhc-debug
   drush dhc-list-enroll
   ```

3. Reset Progress
   ```bash
   # Complete reset
   drush dh-reset
   drush dhc-gen-test --reset --uid=[uid]
   ```

## See Also

- [Requirement Type Templates](requirement-templates.md)
- [Certificate Requirements](requirements.md)
- [Drush Commands](drush.md)
