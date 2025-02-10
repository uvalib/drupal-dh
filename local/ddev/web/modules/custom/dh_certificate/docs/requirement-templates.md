# Requirement Templates

## Overview

Templates define reusable requirement configurations. They provide:
- Standardized validation rules
- Consistent configuration options
- Reusable requirement definitions

## Template Structure

Templates consist of:
- **ID**: Unique identifier
- **Type**: course, skill, or project
- **Label**: Human-readable name
- **Config**: JSON configuration object
- **Validation Rules**: Rules for requirement completion

## Usage

### Command Line

```bash
# Generate template examples
drush dhc-gen-templates --reset

# List templates
drush dhc-lt

# Create template
drush dhc-ct [id] [label] [type] --config='{"key":"value"}'

# Example:
drush dhc-ct core_methods "Core Methods" course --config='{"credits":3,"required":true}'
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

advanced_seminar:
  type: course
  label: "Advanced Seminar"
  config:
    credits: 4
    min_grade: "B"
    prerequisites: ["methods_course"]
    validation:
      requires_paper: true
      min_attendance: 80
```

## Template Inheritance

Templates can inherit from base templates:

```yaml
base_course:
  type: course
  label: "Base Course Template"
  config:
    credits: 3
    required: true

advanced_methods:
  parent: base_course
  label: "Advanced Methods"
  config:
    credits: 4
    specialization: "methods"
```

## Validation Rules

Templates support multiple validation types:
- **Simple**: Basic field comparisons
- **Complex**: Custom validation callbacks
- **Composite**: Multiple rule combinations

Example:
```php
$template->addValidation([
  'type' => 'composite',
  'rules' => [
    ['field' => 'credits', 'op' => '>=', 'value' => 3],
    ['field' => 'grade', 'op' => '>=', 'value' => 'B'],
  ],
]);
```

## Integration

```php
// Load template
$template = RequirementTypeTemplate::load('methods_course');

// Create requirement
$requirement = Requirement::create([
  'template' => $template->id(),
  'type' => $template->getType(),
  'config' => $ template->getConfig(),
]);
```

## Best Practices

1. Use semantic template IDs
2. Document configurations
3. Include validation rules
4. Test with example data
5. Maintain templates centrally
6. Follow naming conventions
7. Version control templates
8. Use inheritance for common patterns
9. Document validation rules
10. Include usage examples

## See Also

- [Requirements Overview](Requirements.md)
- [Progress Tracking](progress.md)