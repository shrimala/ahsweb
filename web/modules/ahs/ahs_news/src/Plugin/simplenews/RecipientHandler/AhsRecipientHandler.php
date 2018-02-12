<?php

namespace Drupal\ahs_news\Plugin\simplenews\RecipientHandler;

use Drupal\simplenews\Plugin\simplenews\RecipientHandlerBase;
use Drupal\simplenews\SubscriberInterface;

/**
 * Base class for all Recipient Handler classes.
 *
 * This handler sends a newsletter issue to all subscribers of a given
 * newsletter.
 *
 * @RecipientHandler(
 *   id = "simplenews_ahs",
 *   title = @Translation("AHS custom")
 * )
 */
class AhsRecipientHandler extends RecipientHandlerBase {

  /**
   * The node entity.
   *
   * @var \Drupal\node\NodeInterface
   */
  public $node;

  /**
   * @param array $configuration
   *   An array of plugin settings.
   * @param String $plugin_id
   *   The id of this plugin.
   * @param $plugin_definition
   *   The annotation metadata for this plugin.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    if ($nid = $configuration['id']) {
      $this->node = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($nid);
    }
    else {
      throw new \Exception("Missing node id in configuration passed to simplenews_ahs plugin");
    }
  }

  /**
   * Implements SimplenewsRecipientHandlerInterface::buildRecipientQuery()
   */
  public function buildRecipientQuery() {
    $newsletterQuery = parent::buildRecipientQuery();
    $newsletterConditions = &$newsletterQuery->conditions();
    kint($newsletterConditions);
    foreach ($newsletterConditions as $key => $condition) {
      if ($condition['field'] === 'subscriptions_target_id') {
        unset($newsletterConditions['$key']);
      }
    }
    $newsletters = array_column($this->node->get('simplenews_issue')
      ->getValue(), 'target_id');
    $newsletterQuery->condition('t.subscriptions_target_id', $newsletters, 'IN');
    return $newsletterQuery;
  }

}
