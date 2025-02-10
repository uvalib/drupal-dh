<?php

namespace Drupal\dh_certificate\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Form controller for RequirementSet edit forms.
 */
class RequirementSetEditForm extends RequirementSetFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // Get requirement counts by type
    $type_counts = [];
    foreach ($this->entity->getRequirements() as $requirement) {
      $type = $requirement->getRequirementType();
      if (!isset($type_counts[$type])) {
        $type_counts[$type] = 0;
      }
      $type_counts[$type]++;
    }

    // Add count badges to vertical tabs menu items
    // if (isset($form['requirements'])) {
      foreach (Element::children($form['requirements']) as $type) {
        $count = $type_counts[$type] ?? 0;
        $original_title = $form['requirements'][$type]['#title'];
        
        // Modify the vertical tab title to include the count
        $form['requirements'][$type]['#title_display'] = 'before';
        $form['requirements'][$type]['#title'] = $original_title . ' <span class="requirement-count">' . $count . '</span>';
        
        // Ensure our markup is not escaped
        $form['requirements'][$type]['#title_display'] = 'raw';
      }

      // Attach our CSS
      $form['#attached']['library'][] = 'dh_certificate/requirement-set-form';
    // }

    return $form;
  }

}
