# Requirement Type Templates

## Overview

The RequirementTypeTemplate system provides a configurable way to define and manage certificate requirements through reusable templates. Each template defines a specific type of requirement with its validation rules and configuration options.

## Quick Start

### Generate Example Templates

```bash
# Generate example templates
drush dhc-gen-ex

# Generate fresh examples (removes existing)
drush dhc-gen-ex --reset
```

This will create standard templates for:
- Core Methods Course
- Elective Course
- Tool Proficiency
- Capstone Project

### Basic Commands

```bash
# List all templates
drush dhc-lt

# Create a template
drush dhc-ct [id] [label] [type] --weight=[weight] --config='[json]'

# Delete a template
drush dhc-dt [id]
```

## Template Types & Examples

### Core Methods Course
```yaml
core_methods:
  label: 'Core Methods Course'
  type: 'course'
  weight: 0
  config:
    min_count: 1
    credits: 3
    course_type: 'methods'
    required: true
```

### Tool Proficiency
```yaml
tool_proficiency:
  label: 'Tool Proficiency'
  type: 'skill'
  weight: 20
  config:
    tools:
      git: 'Git Version Control'
      python: 'Python Programming'
      r: 'R Statistical Computing'
    min_proficiency: 2
```

### Capstone Project
```yaml
capstone_project:
  label: 'Capstone Project'
  type: 'project'
  weight: 30
  config:
    milestones:
      proposal:
        label: 'Project Proposal'
        deadline: '+2 months'
        required: true
      implementation:
        label: 'Implementation'
        deadline: '+4 months'
        required: true
      presentation:
        label: 'Final Presentation'
        deadline: '+6 months'
        required: true
```

## Administration

### User Interface
- List: `/admin/config/dh-certificate/requirement-templates`
- Add: `/admin/config/dh-certificate/requirement-templates/add`
- Edit: `/admin/config/dh-certificate/requirement-templates/{template}/edit`

### Programmatic Usage

```php
// Create template
$template = RequirementTypeTemplate::create([
  'id' => 'advanced_core',
  'label' => 'Advanced Core Course',
  'type' => 'course',
  'weight' => 5,
  'config' => [
    'min_count' => 1,
    'credits' => 3,
    'course_type' => 'advanced'
  ]
]);
$template->save();
```

## Best Practices

1. Use example templates as starting points
   ```bash
   drush dhc-gen-ex
   ```

2. Follow naming conventions
   - Use descriptive IDs: `{type}_{purpose}`
   - Example: `course_methods_intro`

3. Organize with weights
   - Core requirements: 0-10
   - Electives: 11-20
   - Skills: 21-30
   - Projects: 31-40

4. Document configurations
   ```yaml
   # Always include comments for complex configs
   config:
     min_count: 2  # Minimum required completions
     credits: 3    # Credits per completion
   ```

## Troubleshooting

1. View template details:
   ```bash
   drush config:get dh_certificate.requirement_type_template.[template_id]
   ```

2. Reset to examples:
   ```bash
   drush dhc-gen-ex --reset
   ```

3. Common issues:
   - Clear cache after changes: `drush cr`
   - Verify template existence: `drush dhc-lt`
   - Check template usage: `drush dh-certificate:debug-requirements`

## See Also

- [DH Certificate Documentation](README.md)
- [Requirement Types](README.RequirementTypes.md)
- [Progress Tracking](progress.md)