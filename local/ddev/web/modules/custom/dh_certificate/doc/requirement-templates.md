# Requirement Templates

## Overview

Templates define reusable requirement configurations. They provide:
- Standardized validation rules
- Consistent configuration options
- Reusable requirement definitions

## Usage

### Command Line

```bash
# Generate examples
drush dhc-gen-ex

# List templates
drush dhc-lt

# Create template
drush dhc-ct [id] [label] [type] --config='[json]'
```

### Example Templates

```yaml
methods_course:
  type: course
  label: "Methods Course"
  config:
    credits: 3
    required: true

tool_proficiency:
  type: skill
  label: "Tool Proficiency"
  config:
    tools:
      - git
      - python
    min_level: intermediate

capstone:
  type: project
  label: "Capstone"
  config:
    milestones:
      - proposal
      - implementation
      - presentation
```

## Integration

```php
// Load template
$template = RequirementTypeTemplate::load('methods_course');

// Create requirement
$requirement = Requirement::create([
  'template' => $template->id(),
  'type' => $template->getType(),
  'config' => $template->getConfig(),
]);
```

## Best Practices

1. Use semantic template IDs
2. Document configurations
3. Include validation rules
4. Test with example data
5. Maintain templates centrally

## See Also

- [Requirements Overview](Requirements.md)
- [Progress Tracking](progress.md)