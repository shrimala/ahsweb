<?php

namespace Drupal\ahs_cer;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\comment\Entity\Comment;

/**
 * Class simple_cer.
 *
 * @package Drupal\ahs_cer
 */
class simple_cer {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * @param string $thisFieldName
   *  Name of the field on the changed entity which holds entity references
   *  that need corresponding.
   * @param $correspondingFieldName
   *  Name of the field on the referenced entity which holds references that should correspond back.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The changed entity.
   */
  public function updateReferences($thisFieldName, $correspondingFieldName, ContentEntityInterface $entity) {
    // Find out which references have just been added and removed.
    if (empty($entity->original)) {
      // This is a newly created entity.
      $previousReferencedEntities = [];
    }
    else {
      // This entity is being updated.
      $previousReferencedEntities = $entity->original->get($thisFieldName)
        ->referencedEntities();
    }
    $currentReferencedEntities = $entity->get($thisFieldName)
      ->referencedEntities();

    // Detect added/removed references. This correctly ignores order.
    $removedReferencedEntities = $this->nested_array_diff($currentReferencedEntities, $previousReferencedEntities);
    $addedReferencedEntities = $this->nested_array_diff($previousReferencedEntities, $currentReferencedEntities);

    // For removed references, remove this entity from the corresponding field
    foreach ($removedReferencedEntities as $removedReferencedEntity) {
      $this->removeCorrespondingReference($correspondingFieldName, $entity->id(), $removedReferencedEntity);
    }

    // For added references, append this entity to the corresponding field
    foreach ($addedReferencedEntities as $addedReferencedEntity) {
      $this->addCorrespondingReference($correspondingFieldName, $entity->id(), $addedReferencedEntity);
    }

  }

  /**
   * @param string $correspondingFieldName
   *  Name of the field on the referenced entity which holds references back to the
   * referer that need corresponding.
   * @param $refererId
   *  Id of the referencing entity whose update has triggered this.
   * @param $referencedEntity
   *   The referenced entity that needs its corresponding field updating.
   */
  public function addCorrespondingReference($correspondingFieldName, $refererId, ContentEntityInterface $referencedEntity) {
    $correspondingField = $referencedEntity->get($correspondingFieldName);

    // Make sure reference is not already present; this happens for a
    // 'hook within a hook' when a parent updates a child and so the child then
    // tries to update the parent.
    $currentReferences = $correspondingField->getValue();
    // The nesting of the arrays is tricky.
    $newReference = $this->nested_array_diff($currentReferences, [['target_id' => $refererId]]);

    // Add the new reference if there is one
    if (!empty($newReference)) {
      // Is there already new content on the node for the current user?
      $hasNewContent = $this->hasNewcontent($referencedEntity);

      $desiredReferences = $currentReferences;
      // Tricky array nesting continued.
      $desiredReferences[] = $newReference[0];
      $correspondingField->setValue($desiredReferences);
      $referencedEntity->setNewRevision();
      $referencedEntity->setRevisionCreationTime(REQUEST_TIME);
      $referencedEntity->setRevisionUserId(\Drupal::currentUser()->id());
      $referencedEntity->save();

      // If the node did not already have new content for the current user
      // then pretend that the user has just read it, so that the edit we just
      // made will not appear as new content for the user.
      if (!$hasNewContent) {
        history_write($referencedEntity->id());
      }
    }
  }

  /**
   * @param string $correspondingFieldName
   *  Name of the field on the target entity which holds references back to the
   * referer that need corresponding.
   * @param $refererId
   *  Id of the referencing entity whose update has triggered this.
   * @param $referencedEntity
   *   The referenced entity that needs its corresponding field updating.
   */
  public function removeCorrespondingReference($correspondingFieldName, $refererId, ContentEntityInterface $referencedEntity) {
    // Is there already new content on the node for the current user?
    $hasNewContent = $this->hasNewcontent($referencedEntity);

    $correspondingField = $referencedEntity->get($correspondingFieldName);
    $currentReferences = $correspondingField->getValue();
    $desiredReferences = $this->nested_array_diff([['target_id' => $refererId]], $currentReferences);
    $correspondingField->setValue($desiredReferences);
    $referencedEntity->setNewRevision();
    $referencedEntity->setRevisionCreationTime(REQUEST_TIME);
    $referencedEntity->setRevisionUserId(\Drupal::currentUser()->id());
    $referencedEntity->save();

    // If the node did not already have new content for the current user
    // then pretend that the user has just read it, so that the edit we just
    // made will not appear as new content for the user.
    if (!$hasNewContent) {
      history_write($referencedEntity->id());
    }
  }

  protected function nested_array_diff($a1, $a2) {
    $diff = array_diff(array_map('serialize', $a2), array_map('serialize', $a1));
    return (array_map('unserialize', $diff));
  }

  // Sort the references to so their comparison is not affected by reordering.
  //usort($previousReferencedEntities, [$this,"sortById"]);
  //usort($currentReferencedEntities, [$this,"sortById"]);
  protected function sortById(ContentEntityInterface $leftEntity, ContentEntityInterface $rightEntity) {
    $leftId = (int) $leftEntity->id();
    $rightId = (int) $rightEntity->id();
    return (($leftId < $rightId) ? -1 : 1);
  }


  protected function hasNewcontent($entity) {
    $lastContentTimestamp = $entity->getChangedTime();
    if (\Drupal::moduleHandler()->moduleExists('comment')) {
      $lastComment = NULL;
      $lastCid = \Drupal::entityQuery('comment')
        ->condition('entity_id', $entity->id())
        ->condition('entity_type', $entity->getEntityTypeId())
        ->sort('cid', 'DESC')
        ->range(0, 1)
        ->execute();
      $lastCid = reset($lastCid); // value of first element, or false if none.
      if ($lastCid) {
        $lastComment = Comment::load($lastCid);
        if (!empty($lastComment)) {
          $lastCommentTimestamp = $lastComment->getCreatedTime();
          $lastContentTimestamp = max($lastCommentTimestamp, $lastContentTimestamp);
        }
      }

    }
    return (history_read($entity->id()) < $lastContentTimestamp);
  }
}