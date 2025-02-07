# Certificate Requirements Documentation

## Quick Start

```bash
# Full setup with example data
drush dhc-setup --reset --uid=12

# List available templates
drush dhc-lt

# Check requirements status
drush dhc-progress 12
```

## Requirement Types

Three core requirement types are supported:

1. **Course Requirements**
   - Core methods courses
   - Elective courses
   - Specialized seminars

2. **Skill Requirements**
   - Tool proficiencies
   - Technical competencies
   - Research methods

3. **Project Requirements**
   - Capstone projects
   - Research portfolios
   - Final presentations

## Templates

Requirement templates are now managed as config entities:

## Managing Requirements

### Via Admin UI
- View: `/admin/config/dh-certificate/requirement-templates`
- Add: `/admin/config/dh-certificate/requirement-templates/add`
- Edit: `/admin/config/dh-certificate/requirement-templates/{id}`

### Via Drush
```bash
# Create template
drush dhc-ct core_methods "Core Methods" course --config='{"credits":3}'

# List templates
drush dhc-lt

# Delete template
drush dhc-dt core_methods
```

## Best Practices

1. Always use templates
2. Group related requirements
3. Define clear validation rules
4. Document configurations
5. Test with example data

## See Also

- [Progress Tracking](progress.md)
- [Requirement Templates](requirement-templates.md)
