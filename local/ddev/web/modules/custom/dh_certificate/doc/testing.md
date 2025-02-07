# DH Certificate Testing Guide

## Test Structure

The module uses PHPUnit for testing, with two main types of tests:

### Unit Tests
Located in `tests/src/Unit/`, these test individual components in isolation:
- Service classes
- Entity classes
- Utility functions

Example: `ProgressManagerTest.php` tests the progress manager service without database interaction.

### Kernel Tests
Located in `tests/src/Kernel/`, these test integration with Drupal's systems:
- Entity creation/loading
- Configuration
- Service container integration
- Database operations

## Initial Setup

1. Install required testing dependencies:
```bash
# Install testing packages
ddev composer require --dev behat/mink
ddev composer require --dev drupal/core-dev
ddev composer require --dev symfony/phpunit-bridge
ddev composer require --dev phpspec/prophecy-phpunit

# Create phpunit.xml from the template
ddev exec cp local/ddev/web/core/phpunit.xml.dist local/ddev/web/core/phpunit.xml
```

## Running Tests

### Using DDEV
```bash
# Run all tests (with DB connection)
ddev exec 'SIMPLETEST_DB="mysql://db:db@db/db" SIMPLETEST_BASE_URL="http://drupal-dh.ddev.site" phpunit -c /var/www/html/local/ddev/web/core/phpunit.xml /var/www/html/local/ddev/web/modules/custom/dh_certificate/tests'

# Run only unit tests (no DB needed)
ddev exec phpunit -c /var/www/html/local/ddev/web/core/phpunit.xml /var/www/html/local/ddev/web/modules/custom/dh_certificate/tests/src/Unit

# Run only kernel tests (with DB)
ddev exec 'SIMPLETEST_DB="mysql://db:db@db/db" SIMPLETEST_BASE_URL="http://drupal-dh.ddev.site" phpunit -c /var/www/html/local/ddev/web/core/phpunit.xml /var/www/html/local/ddev/web/modules/custom/dh_certificate/tests/src/Kernel'
```

### Project Setup
The project uses a custom docroot configuration:
```yaml
docroot: local/ddev/web
composer_root: local/ddev
```

Therefore, all PHPUnit commands should be run relative to the composer root directory, using the Drupal core PHPUnit configuration.

## Test Configuration
The module uses Drupal core's PHPUnit configuration from `/var/www/html/local/ddev/web/core/phpunit.xml`, which is automatically created from the template if missing.

PHPUnit configuration is automatically managed by DDEV. The system:
- Creates phpunit.xml from the template on container start if it doesn't exist
- Uses Drupal core's configuration from `web/core/phpunit.xml`
- Maintains configuration across container rebuilds

No manual setup is required - the configuration will be automatically created if missing.

## Writing Tests

### Unit Test Example
```php
namespace Drupal\Tests\dh_certificate\Unit;

class MyTest extends UnitTestCase {
  protected function setUp(): void {
    parent::setUp();
    // Mock dependencies
    // Initialize test subject
  }

  public function testSomething() {
    // Arrange
    // Act
    // Assert
  }
}
```

### Kernel Test Example
```php
namespace Drupal\Tests\dh_certificate\Kernel;

class MyTest extends KernelTestBase {
  protected static $modules = ['dh_certificate', 'system'];

  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['dh_certificate']);
  }

  public function testSomething() {
    // Test with real services and database
  }
}
```

## Test Coverage

To generate a code coverage report:
```bash
ddev exec phpunit --coverage-html coverage web/modules/custom/dh_certificate/tests
```

Coverage reports help identify:
- Untested code
- Dead code
- Test effectiveness

## Best Practices

1. Test one thing per test method
2. Use meaningful test names that describe the scenario
3. Follow AAA pattern (Arrange, Act, Assert)
4. Mock external dependencies in unit tests
5. Use data providers for multiple test cases
6. Keep tests focused and maintainable
7. Run tests from the correct directory (composer root)
8. Use Drupal core's PHPUnit configuration
