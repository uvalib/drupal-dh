<?php

namespace Drupal\dh_certificate;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\dh_certificate\Schema\CertificateSchema;

/**
 * Service for synchronizing schema between code and config.
 */
class SchemaSync {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a SchemaSync object.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Updates the config schema to match the code schema.
   */
  public function updateConfig() {
    $config = $this->configFactory->getEditable('dh_certificate.schema');
    $config->set('dh_certificate.enrollments', CertificateSchema::getEnrollmentSchema());
    $config->save();
  }

  /**
   * Validates if config schema matches code schema.
   */
  public function validateConfig(): bool {
    $config = $this->configFactory->get('dh_certificate.schema');
    $config_schema = $config->get('dh_certificate.enrollments');
    return CertificateSchema::validateSchema($config_schema ?? []);
  }
}
