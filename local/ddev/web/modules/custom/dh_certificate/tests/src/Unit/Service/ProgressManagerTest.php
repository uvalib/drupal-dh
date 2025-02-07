<?php

namespace Drupal\Tests\dh_certificate\Unit\Service;

use Drupal\Tests\UnitTestCase;
use Drupal\dh_certificate\Progress\ProgressManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\node\NodeInterface;

/**
 * @coversDefaultClass \Drupal\dh_certificate\Progress\ProgressManager
 * @group dh_certificate
 */
class ProgressManagerTest extends UnitTestCase {

  /**
   * @var \Drupal\dh_certificate\Progress\ProgressManager
   */
  protected $progressManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    
    // Create mock query that returns some test data
    $query = $this->createMock(QueryInterface::class);
    $query->expects($this->any())
      ->method('condition')
      ->willReturnSelf();
    $query->expects($this->any())
      ->method('accessCheck')
      ->willReturnSelf();
    $query->expects($this->any())
      ->method('execute')
      ->willReturn([1, 2]); // Return some fake enrollment IDs

    // Create mock storage with loadMultiple
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects($this->any())
      ->method('getQuery')
      ->willReturn($query);
    $storage->expects($this->any())
      ->method('loadMultiple')
      ->willReturn([
        $this->createMockEnrollment('completed'),
        $this->createMockEnrollment('in_progress'),
      ]);

    // Create mock entity type manager
    $entity_type_manager = $this->createMock('Drupal\Core\Entity\EntityTypeManagerInterface');
    $entity_type_manager->expects($this->any())
      ->method('getStorage')
      ->willReturn($storage);

    $state = $this->createMock('Drupal\Core\State\StateInterface');
    
    // Initialize the progress manager with all required dependencies
    $this->progressManager = new ProgressManager($entity_type_manager, $state);
  }

  protected function createMockFieldItem($value) {
    $field_item = $this->createMock(FieldItemInterface::class);
    $field_item->expects($this->any())
      ->method('getValue')
      ->willReturn(['value' => $value]);
    $field_item->expects($this->any())
      ->method('__get')
      ->with('value')
      ->willReturn($value);
    return $field_item;
  }

  protected function createMockFieldList($value) {
    $field_list = $this->createMock(FieldItemListInterface::class);
    $field_list->expects($this->any())
      ->method('first')
      ->willReturn($this->createMockFieldItem($value));
    return $field_list;
  }

  protected function createMockEnrollment($status) {
    // Create mock course
    $course = $this->createMock(NodeInterface::class);
    $course->expects($this->any())
      ->method('label')
      ->willReturn('Test Course');
    
    // Mock course code field
    $course_code = $this->createMock(FieldItemListInterface::class);
    $course_code->expects($this->any())
      ->method('getValue')
      ->willReturn([['value' => 'TEST101']]);
    $course->expects($this->any())
      ->method('get')
      ->willReturnMap([
        ['field_course_code', $course_code],
      ]);

    // Create status field item
    $status_item = $this->createMock(FieldItemInterface::class);
    $status_item->expects($this->any())
      ->method('__get')
      ->with('value')
      ->willReturn($status);

    // Create status field list
    $status_field = $this->createMock(FieldItemListInterface::class);
    $status_field->expects($this->any())
      ->method('first')
      ->willReturn($status_item);
    
    // Create course reference field item
    $course_ref_item = $this->createMock(FieldItemInterface::class);
    $course_ref_item->expects($this->any())
      ->method('__get')
      ->willReturnMap([
        ['target_id', 1],
        ['entity', $course],
      ]);

    // Create course reference field list
    $course_field = $this->createMock(FieldItemListInterface::class);
    $course_field->expects($this->any())
      ->method('first')
      ->willReturn($course_ref_item);
    
    // Create enrollment entity
    $enrollment = $this->createMock('Drupal\Core\Entity\ContentEntityBase');
    $enrollment->expects($this->any())
      ->method('get')
      ->willReturnMap([
        ['status', $status_field],
        ['course_id', $course_field],
      ]);

    return $enrollment;
  }

  /**
   * @covers ::getUserProgress
   */
  public function testGetUserProgress() {
    // Create a mock user
    $account = $this->createMock(AccountInterface::class);
    $account->method('id')
      ->willReturn(1);

    // Test getting user progress
    $progress = $this->progressManager->getUserProgress($account);
    
    // Verify all expected progress data
    $this->assertNotNull($progress);
    $this->assertEquals(2, $progress['total_courses'], 'Total courses should be 2');
    $this->assertEquals(1, $progress['completed_courses'], 'Completed courses should be 1');
    $this->assertEquals(50, $progress['total_percentage'], 'Percentage should be 50%');
    $this->assertCount(2, $progress['courses'], 'Should have 2 courses in progress data');
    
    // Verify course data structure
    $this->assertArrayHasKey('title', $progress['courses'][0], 'Course should have title');
    $this->assertArrayHasKey('status', $progress['courses'][0], 'Course should have status');
  }

}
