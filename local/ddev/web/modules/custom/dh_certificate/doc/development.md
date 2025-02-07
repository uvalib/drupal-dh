# DH Certificate Development Guide 

## Entity Types

### RequirementTypeTemplate

Config entity for storing requirement template definitions:

```php
use Drupal\dh_certificate\Entity\RequirementTypeTemplate;

$template = RequirementTypeTemplate::create([
  'id' => 'template_id',
  'label' => 'Template Label',
  'type' => 'requirement_type',
  'config' => [],
  'weight' => 0
]);
```

### Course Structure Monitoring

The system now includes structure monitoring capabilities:

```php
// Monitor course structure changes
$monitor = \Drupal::service('dh_certificate.structure_monitor');
$changes = $monitor->detectChanges();
```

## Services

### Progress Manager

```php
$progress_manager = \Drupal::service('dh_certificate.progress');
$progress = $progress_manager->getUserProgress($user);
```

### Structure Monitor 

```php
$monitor = \Drupal::service('dh_certificate.structure_monitor');
$monitor->recordCurrentStructure();
```

## Testing

Run tests using DDEV's Drupal test runner:
```bash
ddev exec phpunit web/modules/custom/dh_certificate
```

Ensure PHPUnit is installed in your DDEV container:
```bash
ddev composer require --dev phpunit/phpunit
```


## Development Setup

1. Enable development mode:
```bash
ddev drush config:set dh_certificate.settings debug true
```

2. Monitor structure changes:
```bash
ddev drush dh-certificate:monitor-record
```

3. Check progress accuracy:
```bash
ddev drush dhc-debug
```

## Testing

Run tests using DDEV's Drupal test runner:
```bash
# Run all module tests
ddev exec ../vendor/bin/phpunit -c web/core/phpunit.xml web/modules/custom/dh_certificate

# Run specific test class
ddev exec ../vendor/bin/phpunit -c web/core/phpunit.xml web/modules/custom/dh_certificate/tests/src/Unit/RequirementTypeTemplateTest.php

# Run with coverage report
ddev exec ../vendor/bin/phpunit -c web/core/phpunit.xml --coverage-html coverage web/modules/custom/dh_certificate
```

Ensure PHPUnit is installed in your DDEV container:
```bash
ddev composer require --dev phpunit/phpunit
```