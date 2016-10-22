<?php

namespace Drupal\ahs_discussions;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;

/**
 * Class Ancestry.
 *
 * @package Drupal\ahs_discussions
 */
class Ancestry {

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
   * Update ancestry field of a discussion based on first value in parents field.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface $discussion
   *    A discussion node that may need its ancestry updating. 
   */
  public function updateOwnAncestry(ContentEntityInterface $discussion) {
    $newFirstParent = $discussion->field_parents->entity;
    $oldFirstParent = empty($discussion->original) ? NULL : $discussion->original->field_parents->entity;

    //If the first parent has changed, update the ancestry.
    if ($newFirstParent !== $oldFirstParent) {
      // If there are no parents, there can be no ancestry
      if (empty($newFirstParent)) {
        $discussion->field_ancestry = [];
      }
      else {
        // Get parent's ancestry
        $newAncestry = $newFirstParent->field_ancestry->getValue();
        // Append parent to parent's ancestry
        $newAncestry[] = ["target_id" => $newFirstParent->id()];
        if (!$this->withinAncestry($discussion, $newAncestry)) {
          $discussion->field_ancestry = $newAncestry;
        }
      }
    // Update the ancestry_plain field as well.
    $discussion->field_ancestry_plain = $this->makePlain($discussion->field_ancestry);
    }

    return $discussion;
  }

  /**
   * Update ancestry field of any child that has this as its first parent.
   *
   * @var \Drupal\Core\Entity\EntityInterface $discussion
   *    A discussion node that may have had its ancestry updating.
   */
  public function updateChildrensAncestry(ContentEntityInterface $discussion) {
    // If own ancestry has changed, update children's ancestry.
    $newAncestry = $discussion->field_ancestry->getValue();
    $oldAncestry = empty($discussion->original) ? NULL : $discussion->original->field_ancestry->getValue();
    if ($newAncestry !== $oldAncestry) {
      $children = $discussion->field_children->referencedEntities();
      $childAncestry = $newAncestry;
      $childAncestry[] = ["target_id" => $discussion->id()];
      foreach ($children as $child) {
        // Only update children's ancestry if they are a discussion.
        if ($child->bundle() == 'discussion') {
          // Only update children's ancestry if this is their first parent.
          if ($child->field_parents->entity->id() === $discussion->id()) {
            // Only update children's ancestry if child is not already included.
            if (!$this->withinAncestry($child, $childAncestry)) {
              $child->field_ancestry = $childAncestry;
              $child->field_ancestry_plain = $this->makePlain($child->field_ancestry);
              $child->save();
            }
          }
        }
      }
    }
  }

  /**
   * Convert a multiple value entity reference field into a string of labels .
   *
   * @var \Drupal\Core\Field\EntityReferenceFieldItemListInterface $fieldItems
   *    The data from an entity reference field.
   */
  protected function makePlain(EntityReferenceFieldItemListInterface $fieldItems) {
    $referencedEntities = $fieldItems->referencedEntities();
    $plainEntities = '';

    foreach ($referencedEntities as $referencedEntity) {
      if ($plainEntities !== '') {
        $plainEntities .= '/';
      }
      $plainEntities .= $referencedEntity->label();
    }
    return $plainEntities;
  }

  /**
   * Detect whether an entity is already reference within an ancestry ER array.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface $discussion
   *    A discussion node entity.
   * @var array $ancestry
   *    A discussion node entity, coming from field_ancestry->getValue().
   */
  protected function withinAncestry(ContentEntityInterface $entity, $ancestry) {
    $detected = FALSE;
    foreach ($ancestry as $ancestor) {
      if ($entity->id() == $ancestor['target_id']) {
        $detected = TRUE;
      }
    }
    return $detected;
  }

}
