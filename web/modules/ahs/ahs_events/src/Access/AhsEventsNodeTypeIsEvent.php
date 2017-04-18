<?php

namespace Drupal\ahs_events\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Checks access for displaying configuration translation page.
 */
class AhsEventsNodeTypeIsEvent implements AccessInterface {

  /**
   * A custom access check.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function access(NodeInterface $node) {
    //\Drupal\Core\Routing\RouteMatch $route_match
    //NodeInterface $node
    //$node = $route_match->getParameter('node');
    // Check permissions and combine that with any custom access checking needed. Pass forward
    // parameters from the route and/or request as needed.
    //return $account->hasPermission('access content') && $this->someOtherCustomCondition();
    return AccessResult::allowedIf($node->bundle() === 'event');
    //return TRUE;
  }

}