<?php
namespace Drupal\ahs_discussions;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\Entity\Comment;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;

/**
 * Form handler for the discussion form.
 */
class DiscussionForm extends ContentEntityForm {

  protected $oldRevisionId;

  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /*
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
    */

    $form['comment'] = array(
      '#type' => 'text_format',
      '#title' => 'Make a comment',
      //'#format' => 'basic_html',
      '#allowed_formats' => ['basic_html',],
      '#rows' => 4, // doesn't work with WYSIWYG
      '#weight' => '100',
      '#attributes' => ['placeholder' => t('Make a comment'),], // doesn't work with WYSIWYG
    );

    //$form['menu']['#access']=FALSE;
    $form['revision_log']['#access'] = FALSE;
    $form['actions']['delete']['#access'] = FALSE;


    /*
        $form['revision_log']['#states'] = [
          'visible' => [
            ':textarea[name="body[0][value]"]' => [
              'data-editor-value-is-changed' => 'true'
            ],
          ],
        ];
    */

    $form['promote']['#states'] = [
      'visible' => [
        'input[name="field_help_wanted[value]"]' => [
          'checked' => TRUE,
        ],
      ],
    ];

    //$form['#attached']['library'][] = 'ahs_discussions/revision_log';
    //$form['comment'] = $node->field_kbk->view(array('type' => 'some_formatter'));

    // Access is denied to the comments field
    // for user who are not administrators. This is because
    // this is technically the node edit form where comments are
    // normally administered not displayed, and so the 'administer comments'
    // permission is being used not the 'access comments' permission.
    $user = \Drupal::currentUser();
    if ($user->hasPermission('access comments')) {
      $form['field_comments_with_changes']['#access'] = TRUE;
    }

    // Adjust the UI for parents and children fields.
    $form['field_parents']['#attributes']['class'][] = 'ahs-preview-hide-edit-if-hideui';
    $form['field_children']['#attributes']['class'][] = 'ahs-preview-hide-edit-if-hideui';
    $form['field_parents']['#attributes']['class'][] = 'ahs-preview-hideui-requested';
    $form['field_children']['#attributes']['class'][] = 'ahs-preview-hideui-requested';
    $form['field_parents']['#attributes']['class'][] = 'ahs-preview-hide-move-requested';
    $form['field_children']['#attributes']['class'][] = 'ahs-preview-hide-move-requested';
    $form['field_parents']['#attributes']['class'][] = 'ahs-preview-hide-remove-requested';
    $form['field_children']['#attributes']['class'][] = 'ahs-preview-hide-remove-requested';
    $form['field_parents']['#attributes']['class'][] = 'ahs-preview-hide-new-requested';
    $form['field_children']['#attributes']['class'][] = 'ahs-preview-hide-new-requested';
    $form['field_parents']['#attached']['library'][] = 'ahs_discussions/hideui';
    $form['field_children']['#attached']['library'][] = 'ahs_discussions/hideui';

    $form['body']['#attributes']['class'][] = 'ahs-preview-hide-edit';
    $form['body']['#attributes']['class'][] = 'ahs-preview-hideui-requested';
    $form['body']['#attributes']['class'][] = 'ahs-preview-hide-move-requested';
    $form['body']['#attributes']['class'][] = 'ahs-preview-hide-new-requested';
    $form['body']['#attached']['library'][] = 'ahs_discussions/hideui';

    $form['field_files']['#attributes']['class'][] = 'ahs-preview-hide-edit';
    $form['field_files']['#attributes']['class'][] = 'ahs-preview-hideui-requested';
    $form['field_files']['#attributes']['class'][] = 'ahs-preview-hide-remove-requested';
    $form['field_files']['#attributes']['class'][] = 'ahs-preview-hide-move-requested';
    $form['field_files']['#attributes']['class'][] = 'ahs-preview-hide-new-requested';
    $form['field_files']['#attached']['library'][] = 'ahs_discussions/hideui';

    if ($form['field_children']['widget']['#max_delta'] === 0) {
      $form['field_children']['widget'][0]['preview_container']['preview'] = ['#markup' => '<p class="ahs-preview-empty">No other discussions have been included yet as part of this one.</p>',];
      $form['field_children']['#attributes']['class'][] = 'ahs-preview-empty';
    }
    if ($form['field_parents']['widget']['#max_delta'] === 0) {
      $form['field_parents']['widget'][0]['preview_container']['preview'] = ['#markup' => '<ol class="breadcrumb"><li><a href="/">Home</a></li></ol>',];
      $form['field_parents']['#attributes']['class'][] = 'ahs-preview-empty';
    }
    if ($form['body']['widget'][0]['preview_container']['preview'] == array()) {
      $form['body']['widget'][0]['preview_container']['preview'] = ['#markup' => '<p class="ahs-preview-empty">No discussion summary has been created yet.</p>',];
    }
    if ($form['field_files']['widget']['#file_upload_delta'] == 0) {
      $form['field_files']['empty_message'] = ['#markup' => '<p class="ahs-preview-empty">No files have been uploaded yet.</p>',];
    }

    $form['field_participants']['widget']['#attributes']['data-placeholder'] = "Invite other people to this discussion";
    $form['field_assigned']['widget']['#attributes']['data-placeholder'] = "Assign as a task to someone";

    if ($this->entity->field_assigned->getValue()) {
      $form['#attributes']['class'][] = "ahs-assigned";
    }
    if ($this->entity->field_private->value) {
      $form['#attributes']['class'][] = "ahs-private";
    }
    if ($this->entity->field_help_wanted->value) {
      $form['#attributes']['class'][] = "ahs-help-wanted";
    }
    if ($this->entity->promote->value) {
      $form['#attributes']['class'][] = "ahs-promote";
    }
    if ($this->entity->field_finished->value) {
      $form['#attributes']['class'][] = "ahs-finished";
    }
    
    //if ($this->getEntity()->field_top_level_category == FALSE) {}
  //  $form['field_parents']['widget']['0']['target_id']['#required'] = TRUE;
   // $form['field_parents']['widget']['0']['target_id']['#required_error'] = t('Please choose a discussion that this is part of.');
/*
    $form['field_children']['ahs_preview_empty_message'] = [
      '#markup' => '<p class="ahs-preview-empty-message">No discussions have been included yet.</p>',
    ];
*/
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareEntity() {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entity;
    if (!$node->isNew()) {
      // Remove the revision log message from the original node entity.
      $node->revision_log = NULL;
    }
  }

  /**
   * {@inheritdoc}
   *
   * Always create a new revision
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Build the node object from the submitted values.
    parent::submitForm($form, $form_state);

    // If not a new entity, get the original
    $original = NULL;
    if ($this->entity->id()) {
      $storage = \Drupal::entityManager()->getStorage('node');
      $original = $storage->loadUnchanged($this->entity->id());
    }
    $this->addUserAsParticipant($original);

    // Set new revision if needed
    if ($this->entity->id()) {
      $this->considerNewRevision($form_state, $original);
    }
  }

  protected function considerNewRevision(FormStateInterface $form_state, $original) {

    // Get a list of fields to evaluate for changes
    $entityManager = \Drupal::service('entity.manager');
    $fields = $entityManager->getFieldDefinitions('node', 'discussion');
    // Ignore the 'changed' property that has
    // already been updated in the parent ContentEntityForm
    unset($fields['changed']);
    unset($fields['field_comments_with_changes']);

    // If any field has changed, create a new revision.
    foreach ($fields as $fieldName => $field) {
      if ($this->entity->$fieldName->getValue() != $original->$fieldName->getValue()) {
        // Create a new revision.
        $this->entity->oldRevisionId = $this->entity->getRevisionId();
        $this->entity->setNewRevision();
        $this->entity->setRevisionCreationTime(REQUEST_TIME);
        $this->entity->setRevisionUserId(\Drupal::currentUser()->id());
        break;
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $node = $this->entity;

    // Add a comment if the user entered one on the form
    $hasComment = !empty($form_state->getValue('comment')['value']);
    if ($hasComment) {
      $comment = [
        'comment_type' => 'comments_with_changes',
        'field_name' => 'field_comments_with_changes',
        'comment_body' => $form_state->getValue('comment'),
      ];
      \Drupal::service('changes.comment_with_changes')
        ->add($comment, $node);
      drupal_set_message(t('Your comment has been shared.'));
    }
    //$this->addComment($form, $form_state, $insert, $update);


    $insert = $node->isNew();
    $update = $node->isNewRevision();
    $node->save();

    // Produce messages and logs
    $node_link = $node->link($this->t('View'));
    $context = array('@type' => $node->getType(), '%title' => $node->label(), 'link' => $node_link);
    if ($insert) {
      $this->logger('content')->notice('Started @type "%title".', $context);
      drupal_set_message(t('You have started a new discussion.'));
    }
    elseif ($update) {
      $this->logger('content')->notice('Updated @type "%title".', $context);
      drupal_set_message(t('Your changes to the discussion have been saved.'));
    }

    // Redirect to discussion
    if ($node->id()) {
      $form_state->setValue('nid', $node->id());
      $form_state->set('nid', $node->id());
      if ($node->access('view')) {
        $form_state->setRedirect(
          'entity.node.canonical',
          array('node' => $node->id())
        );
      }
      else {
        $form_state->setRedirect('<front>');
      }

    }
    else {
      // In the unlikely case something went wrong on save, the node will be
      // rebuilt and node form redisplayed the same way as in preview.
      drupal_set_message(t('The discussion could not be saved.'), 'error');
      $form_state->setRebuild();
    }

  }

  protected function addUserAsParticipant($original) {
    $currentUser = \Drupal::currentUser()->id();
    if (!is_null($original)) {
      foreach ($original->field_participants->getValue() as $originalParticipants) {
        // If the current user was originally listed, then he is either
        // still there, or was deliberately removed.
        if ($originalParticipants['target_id'] === $currentUser) {
          return;
        }
      }
    }
    foreach ($this->entity->field_participants->getValue() as $participant) {
      // If the current user is listed, then no need to do anything.
      if ($participant['target_id'] === $currentUser) {
        return;
      }
    }
    // If not originally or currently listed, add the current user.
    $values = $this->entity->field_participants->getValue();
    $values[]= ['target_id' => $currentUser];
    $this->entity->field_participants->setValue($values);
  }

  // deprecated, remove
  protected function addComment (array $form, FormStateInterface $form_state, $insert, $update) {
    $hasComment = !empty($form_state->getValue('comment')['value']);
    $newRevisionId = NULL;
    if ($update) {
      $newRevisionId = $this->entity->getRevisionId();
    }

    // Prepare the comment data
    $comment = [
      'comment_type' => 'comments_with_changes',
      'field_name' => 'field_comments_with_changes',
    ];
    if ($hasComment) {
      $comment['comment_body'] = $form_state->getValue('comment');
    }
    else {
      $comment['subject'] = 'Change record';
    }

    //Create the comment
    if ($insert) {
      $comment['comment_body'] = [
        'value' => "<p>Started this discussion.</p>",
        'format' => "full_html",
      ];
      \Drupal::service('changes.comment_with_changes')
        ->add($comment, $this->entity, NULL, $this->entity->getRevisionId(), 'field_changes');
    }
    elseif ($hasComment) {
      //\Drupal::service('changes.comment_with_changes')
      //  ->add($comment, $this->entity, $this->entity->oldRevisionId, $newRevisionId, 'field_changes');
      \Drupal::service('changes.comment_with_changes')
        ->add($comment, $this->entity, NULL, NULL, NULL);
    }

    if ($hasComment) {
      drupal_set_message(t('Your comment has been shared.'));
    }
  }

}