<?php
namespace Drupal\ahs_discussions;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
/**
 * Form handler for the discussion form.
 */
class DiscussionForm extends ContentEntityForm {

  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // Move the revision log to be immediately after the body,
    // first moving all other items after body along to make way.
    $bodyWeight = $form['body']['#weight'];
    foreach ($form as $elementName => $elementArray) {
      if (isset($elementArray['#weight'])) {
        if ($elementArray['#weight'] > $bodyWeight) {
          $form[$elementName]['#weight'] = $form[$elementName]['#weight'] + 1;
        }
      }
    }
    $form['revision_log']['#weight'] = $bodyWeight + 1;

    /*
        $form['revision_log']['#states'] = [
          'visible' => [
            ':textarea[name="body[0][value]"]' => [
              'data-editor-value-is-changed' => 'true'
            ],
          ],
        ];
    */

    //$form['#attached']['library'][] = 'ahs_discussions/revision_log';
    //$form['comment'] = $node->field_kbk->view(array('type' => 'some_formatter'));

    return $form;
  }


}