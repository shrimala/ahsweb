<?php

namespace Drupal\ahs_discussions\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Custom node redirect controller
 */
class DiscussionAddController extends ControllerBase {

  /**
   * Provides the discussion submission form.
   *
   * @param \Drupal\node\NodeTypeInterface $node_type
   *   The node type entity for the node.
   *
   * @return array
   *   A discussion submission form.
   */
  public function add() {
    $node = $this->entityManager()->getStorage('node')->create(array(
      'type' => 'discussion',
    ));
    $form = $this->entityFormBuilder()->getForm($node, "ahs_discuss");
    //$form['revision_log']['#access'] = FALSE;
    $form['field_parents']['widget']['#required'] = TRUE;
    if(($key = array_search('er-enhanced-hideui-requested', $form['field_parents']['#attributes']['class'])) !== false) {
      unset($form['field_parents']['#attributes']['class'][$key]);
    }
    $form['field_parents']['er-enhanced-edit'] = NULL;
    $form['field_comments_with_changes']['#access'] = FALSE;
    $form['comment']['#access'] = FALSE;
    return $form;
  }


}