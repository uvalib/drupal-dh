<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Controller for requirement set operations.
 */
class RequirementSetController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a RequirementSetController.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Enables a requirement set.
   *
   * @param string $requirement_set
   *   The requirement set ID.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response.
   */
  public function enable($requirement_set) {
    try {
      $entity = $this->entityTypeManager
        ->getStorage('requirement_set')
        ->load($requirement_set);

      if ($entity) {
        $entity->set('status', TRUE);
        $entity->save();
        $this->messenger()->addStatus($this->t('Requirement set %label has been enabled.', [
          '%label' => $entity->label(),
        ]));
      }
      else {
        $this->messenger()->addError($this->t('Requirement set not found.'));
      }
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Error enabling requirement set: @error', [
        '@error' => $e->getMessage(),
      ]));
      \Drupal::logger('dh_certificate')->error('Error enabling requirement set: @error', [
        '@error' => $e->getMessage(),
      ]);
    }

    return $this->redirect('dh_certificate.requirement_sets');
  }

  /**
   * Disables a requirement set.
   *
   * @param string $requirement_set
   *   The requirement set ID.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response.
   */
  public function disable($requirement_set) {
    try {
      $entity = $this->entityTypeManager
        ->getStorage('requirement_set')
        ->load($requirement_set);

      if ($entity) {
        // Check if this is the only active set
        $active_sets = $this->entityTypeManager
          ->getStorage('requirement_set')
          ->loadByProperties(['status' => TRUE]);

        if (count($active_sets) === 1 && $entity->status()) {
          $this->messenger()->addWarning($this->t('Cannot disable the only active requirement set.'));
          return $this->redirect('dh_certificate.requirement_sets');
        }

        $entity->set('status', FALSE);
        $entity->save();
        $this->messenger()->addStatus($this->t('Requirement set %label has been disabled.', [
          '%label' => $entity->label(),
        ]));
      }
      else {
        $this->messenger()->addError($this->t('Requirement set not found.'));
      }
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Error disabling requirement set: @error', [
        '@error' => $e->getMessage(),
      ]));
      \Drupal::logger('dh_certificate')->error('Error disabling requirement set: @error', [
        '@error' => $e->getMessage(),
      ]);
    }

    return $this->redirect('dh_certificate.requirement_sets');
  }

}
