<?php

namespace Drupal\Tests\dh_certificate\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Test basic module functionality.
 *
 * @group dh_certificate
 */
class DHCertificateKernelTest extends KernelTestBase {

  protected static $modules = [
    'system',
    'user',
    'field',
    'node',
    'dh_certificate',
  ];

  protected function setUp(): void {
    parent::setUp();

    // Only install schemas we need
    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('user');
    // Don't install config since we don't provide any
  }

  public function testModuleInstallation() {
    $module_handler = $this->container->get('module_handler');
    $this->assertTrue($module_handler->moduleExists('dh_certificate'));
    
    // Test that required services exist
    $this->assertNotNull($this->container->get('dh_certificate.progress'));
  }

}
