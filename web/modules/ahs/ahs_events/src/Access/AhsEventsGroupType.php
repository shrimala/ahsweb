<?php

namespace Drupal\ahs_events\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\Routing\Route;
use Drupal\group\Entity\GroupInterface;


/**
 * Checks access for an entity based on its bundle.
 */
class AhsEventsGroupType implements AccessInterface {

  /**
   * Restrict access based on group type.
   *
   * @param \Drupal\Core\entity\EntityInterface $entity
   *   The entity to check access for.
   * @param \Symfony\Component\Routing\Route $route
   *   The route to check against.
   */
  public function access(GroupInterface $group, Route $route) {
    $group_type = $route->getRequirement('_group_type');
    return AccessResult::allowedIf($group->bundle() === $group_type);
  }

}