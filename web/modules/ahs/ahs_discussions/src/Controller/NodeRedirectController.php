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
    // Redirect to the edit path on the discussion type
    if ($node->getType() == 'discussion') {
      $url = Url::fromRoute('entity.node.ahs_discuss', ['node' => $node->id()]);
      return \Drupal::service('entity.form_builder')->getForm($node, 'ahs_discuss');
      //return new RedirectResponse($url->toString());
    }
    // Otherwise, fall back to the parent route controller.
    else {
      return parent::view($node, $view_mode, $langcode);
    }
  }


}