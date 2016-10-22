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

    // Get last comment; can be null if no previous comments exist
    $lastCommentValues = $entity->{$comment['field_name']}->getValue()[0];
    $lastComment = Comment::load($lastCommentValues['cid']);
    $lastSubject = NULL;
    if (!empty($lastComment)) {
      $lastSubject = $lastComment->getSubject();
    }

    $this->setDefaultSubjectIfNeeded($comment);

    // Evaluate whether to update last comment or create new one
    $hasLastSameAuthor = ($lastCommentValues['last_comment_uid'] === $comment['uid']);
    $isLastWithinPeriod = ((REQUEST_TIME - $lastCommentValues['last_comment_timestamp']) < 30);
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
    $entityType = $entity->getEntityTypeId();

    // If there is a new revision of the host entity, update it on the comment
    if (!empty($newRevisionId)) {
      // If the last comment did not store changes, store the current revisions pair
      if (empty($lastComment->$diffFieldName->entity_type)) {
        $lastComment->$diffFieldName->setValue([
          [
            'left_rid' => $oldRevisionId,
            'right_rid' => $newRevisionId,
          ]
        ]);
      }
      // If changes have already been stored, don't overwrite the older revision (left_rid)
      elseif ($lastComment->$diffFieldName->entity_type === $entityType) {
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

      $lastComment->setCreatedTime(REQUEST_TIME);
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