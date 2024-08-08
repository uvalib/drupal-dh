<?php

namespace Drupal\migrate_drupal7_content\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for nodes.
 *
 * @MigrateSource(
 *   id = "d7_node"
 * )
 */
class NodeSource extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Query to fetch nodes from Drupal 7 database.
    $query = $this->select('node', 'n')
      ->fields('n', [
        'nid',
        'vid',
        'type',
        'title',
        'uid',
        'status',
        'created',
        'changed',
      ])
      ->condition('n.type', 'people', '=');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    // Define the fields available in this source.
    echo "fields";
    return [
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Revision ID'),
      'type' => $this->t('Content type'),
      'title' => $this->t('Title'),
      'uid' => $this->t('User ID'),
      'status' => $this->t('Published status'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Updated timestamp'),
      'orcid' => $this->t('ORCID URL'),
      'photo' => $this->t('Featured Image URI'),
      'name' => $this->t('Name'),
      'body' => $this->t('Body'),
      'email' => $this->t('Email'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('nid');

    // Fetch name
    $name_result = $this->getDatabase()->query('
      SELECT name
      FROM {users}
      WHERE uid = :uid
    ', [':uid' => $row->getSourceProperty('uid')]);

    if ($name = $name_result->fetchField()) {
      $row->setSourceProperty('name', $name);
    }

    // Fetch body content
    $body_result = $this->getDatabase()->query('
      SELECT body_value
      FROM {field_data_body}
      WHERE entity_id = :nid
    ', [':nid' => $nid]);

    if ($body = $body_result->fetchField()) {
      $row->setSourceProperty('body', $body);
    }

    // Fetch email
    $email_result = $this->getDatabase()->query('
      SELECT field_email_email
      FROM {field_data_field_email}
      WHERE entity_id = :nid
    ', [':nid' => $nid]);

    if ($email = $email_result->fetchField()) {
      $row->setSourceProperty('email', $email);
    }

    // Fetch featured image (photo) data
    $featured_image_result = $this->getDatabase()->query('
      SELECT uri
      FROM {file_managed} fm
      INNER JOIN {field_data_field_featured_image} fi ON fm.fid = fi.field_featured_image_fid
      WHERE fi.entity_id = :nid
    ', [':nid' => $nid]);

    // Initialize photo field
    $photo = NULL;

    // Assuming there's at most one featured image
    if ($image_record = $featured_image_result->fetchAssoc()) {
      $photo = $image_record['uri'];
    }

    // Set the photo URI in the row
    if ($photo) {
      $row->setSourceProperty('photo', $photo);
    }

    return parent::prepareRow($row);
  }
}
