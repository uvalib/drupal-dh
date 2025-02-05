<?php

namespace Drupal\dh_certificate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\dh_certificate\Entity\RequirementSet;

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
   * @param \Drupal\dh_certificate\Entity\RequirementSet $requirement_set
   *   The requirement set entity.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response.
   */
  public function enable(RequirementSet $requirement_set) {
    $requirement_set->setStatus(TRUE);
    $requirement_set->save();
    $this->messenger()->addStatus($this->t('Requirement set %label has been enabled.', [
      '%label' => $requirement_set->label(),
    ]));
    return $this->redirect('entity.requirement_set.collection');
  }

  /**
   * Disables a requirement set.
   *
   * @param \Drupal\dh_certificate\Entity\RequirementSet $requirement_set
   *   The requirement set entity.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response.
   */
  public function disable(RequirementSet $requirement_set) {
    $requirement_set->setStatus(FALSE);
    $requirement_set->save();
    $this->messenger()->addStatus($this->t('Requirement set %label has been disabled.', [
      '%label' => $requirement_set->label(),
    ]));
    return $this->redirect('entity.requirement_set.collection');
  }

}
