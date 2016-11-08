<?php

namespace Drupal\ahs_discussions\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Controller\NodeViewController;
//use Drupal\ahs_discussions\DiscussionForm;
use Drupal\Core\Url;

/**
 * Custom node redirect controller
 */
class NodeRedirectController extends NodeViewController {

  public function view(EntityInterface $node, $view_mode = 'full', $langcode = NULL) {
    // Redirect the discussion type
    if ($node->getType() == 'discussion') {
      $user = $this->currentUser->id();
      $participants = array_column($node->field_participants->getValue(), 'target_id');

      // If it is a private discussion & the user is not a participant, block access.
      if ($node->field_private->value && !in_array($user, $participants)) {
        //$url = Url::fromRoute('entity.node.ahs_discuss', ['node' => $node->id()]);
        //return new RedirectResponse($url->toString());
        drupal_set_message(t('Sorry, this discussion is private and you are not listed as a participant.'), 'warning');
        return array();
      }
      else {
        return \Drupal::service('entity.form_builder')->getForm($node, 'ahs_discuss');
      }
    }
    // Otherwise, fall back to the parent route controller.
    else {
      return parent::view($node, $view_mode, $langcode);
    }
  }

}