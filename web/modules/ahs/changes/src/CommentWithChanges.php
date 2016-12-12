<?php

namespace Drupal\changes;

use Drupal\comment\CommentInterface;
use Drupal\comment\Entity\Comment;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;


/**
 * Class CommentWithChanges.
 *
 * @package Drupal\changes
 */
class CommentWithChanges {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   */
  public function __construct() {

  }

  public function add($comment, $entity, $oldRevisionId = NULL, $newRevisionId = NULL, $diffFieldName = NULL) {
    // If the author of the change recently made another change or comment
    // update that comment instead of making a new comment.
    // This keeps the comments easier to read and reduces notification noise.
    //expect array with keys mandatory keys
    // 'comment_type', 'field_name'
    // and optional keys 
    // 'subject', 'comment_body' and 'uid'.

    // We omit to test comment_type is same current and last
    // also will blow up if new fields added

    //Default to current user as author
    if (!isset($comment['uid'])) {
      $comment['uid'] = \Drupal::currentUser()->id();
    }

    // Get last comment; can be null if no previous comments exist.
    // We cannot use the values of the last comment stored on the entity,
    // as these are only updated when the entity is loaded.
    $lastSubject = NULL;
    $lastAuthor = NULL;
    $lastTime = NULL;
    $lastComment = NULL;
    $lastCid = \Drupal::entityQuery('comment')
      ->condition('entity_id', $entity->id())
      ->condition('entity_type', $entity->getEntityTypeId())
      ->sort('cid', 'DESC')
      ->range(0,1)
      ->execute();
    $lastCid = reset($lastCid); // value of first element, or false if none.
    if ($lastCid) {
      $lastComment = Comment::load($lastCid);
      if (!empty($lastComment)) {
        $lastSubject = $lastComment->getSubject();
        $lastAuthor = $lastComment->getOwnerId();
        $lastTime = $lastComment->getChangedTime();
      }
    }
    $this->setDefaultSubjectIfNeeded($comment);

    // Evaluate whether to update last comment or create new one
    $hasLastSameAuthor = ($lastAuthor === $comment['uid']);
    $isLastWithinPeriod = TRUE; //((REQUEST_TIME - $lastTime) < 30);
    $isLastChangeRecord = ($lastSubject === 'Change record');
    $isNewChangeRecord = ($comment['subject'] === 'Change record');
    if ($lastComment && $hasLastSameAuthor && $isLastWithinPeriod && ($isLastChangeRecord || $isNewChangeRecord)) {
      $this->updateLast($comment, $oldRevisionId, $newRevisionId, $diffFieldName, $entity, $lastComment);
    }
    else {
      $this->createNew($comment, $oldRevisionId, $newRevisionId, $diffFieldName, $entity);
    }
  }

  protected function updateLast($comment, $oldRevisionId, $newRevisionId, $diffFieldName, $entity, CommentInterface $lastComment) {
    // If there is a new revision of the host entity, update it on the comment
    if (!empty($newRevisionId)) {
      // If the last comment did not store changes, store the current revisions pair
      if (empty($lastComment->$diffFieldName->right_rid)) {
        $lastComment->$diffFieldName->setValue([
          [
            'left_rid' => $oldRevisionId,
            'right_rid' => $newRevisionId,
          ]
        ]);
      }
      // If changes have already been stored, don't overwrite the older revision (left_rid)
      else {
        $changes = $lastComment->$diffFieldName->getValue();
        $changes[0]['right_rid'] = $newRevisionId;
        $lastComment->$diffFieldName->setValue($changes);
      }
    }

    // Overwrite the old subject only if it was a change record.
    if ($lastComment->subject->value === 'Change Record') {
      $lastComment->setSubject($comment['subject']);
    }

    // Set all fields for which data has been passed
    // and there is no saved value
    foreach ($comment as $name => $value) {
      if (!isset($lastComment->$name->value)) {
        if (is_array($value)) {
          $lastComment->$name->setValue($value);
        }
        else {
          $lastComment->$name->value = $value;
        }
      }
    }

      $lastComment->setChangedTime(REQUEST_TIME);
      $lastComment->save();
  }

  protected function createNew($comment, $oldRevisionId, $newRevisionId, $diffFieldName, $entity) {
    // Create the comment
    $commentDefaults = [
      'langcode' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      'entity_id' => $entity->id(),
      'entity_type' => $entity->getEntityTypeId(),
      'status' => 1,
    ];

    //Array merge: If the input arrays have the same string keys,
    // then the later value for that key will overwrite the previous one.
    $comment = array_merge($commentDefaults, $comment);

    // If we have a revisionId, fill that field.
    if (!empty($newRevisionId)) {
      $comment[$diffFieldName] = [
        'left_rid' => $oldRevisionId,
        'right_rid' => $newRevisionId,
      ];
    }

    $comment = Comment::create($comment);
    $comment->setCreatedTime(REQUEST_TIME);
    $comment->setChangedTime(REQUEST_TIME);
    $comment->save();
  }

  protected function setDefaultSubjectIfNeeded(&$comment) {
    // Extract subject from comment body.
    if (!isset($comment['subject'])){
      $comment['subject'] = '';
    }
    if ((trim($comment['subject']) == '')) {
      if (isset($comment['comment_body'])) {
        // The body may be in any format, so:
        // 1) Filter it into HTML
        // 2) Strip out all HTML tags
        // 3) Convert entities back to plain-text.
        $comment_text = $comment['comment_body']['value'];
        $comment['subject'] = Unicode::truncate(trim(Html::decodeEntities(strip_tags($comment_text))), 29, TRUE, TRUE);
      }
      // Edge cases where the comment body is populated only by HTML tags will
      // require a default subject.
      if ($comment['subject'] == '') {
        $comment['subject'] = t('(No subject)');
      }
    }
  }
}