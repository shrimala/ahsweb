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

    //$form['#attached']['library'][] = 'ahs_discussions/revision_log';
    //$form['comment'] = $node->field_kbk->view(array('type' => 'some_formatter'));

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

    // Set new revision if needed
    if ($this->entity->id()) {
      $this->setNewRevision($form_state);
    }
  }

  protected function setNewRevision(FormStateInterface $form_state) {
    $storage = \Drupal::entityManager()->getStorage('node');
    $original = $storage->loadUnchanged($this->entity->id());

    // Get a list of fields to evaluate for changes
    $entityManager = \Drupal::service('entity.manager');
    $fields = $entityManager->getFieldDefinitions('node', 'discussion');
    // Ignore the 'changed' property that has
    // already been updated in the parent ContentEntityForm
    unset($fields['changed']);

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
    $insert = $node->isNew();
    $update = $node->isNewRevision();
    $node->save();
    $this->addComment($form, $form_state, $insert, $update);

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
    elseif ($hasComment || $update) {
      \Drupal::service('changes.comment_with_changes')
        ->add($comment, $this->entity, $this->entity->oldRevisionId, $newRevisionId, 'field_changes');
    }

    if ($hasComment) {
      drupal_set_message(t('Your comment has been shared.'));
    }
  }

}