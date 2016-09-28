<?php

/**
 * @file
 * Contains \Drupal\my_module\Plugin\Block\NodeMenuBlock.
 */

namespace Drupal\ahs_comments_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * @Block(
 *   id = "ahs_comments_block",
 *   admin_label = @Translation("Node Comments Block"),
 *   category = @Translation("AHS"),
 *   context = {
 *     "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node")
 *     )
 *   }
 * )
 */
class CommentsBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->getContextValue('node');
    $nid_fld = $node->nid->getValue();
    $nid = $nid_fld[0]['value'];

    $markup = '';
    $links = ['entity.node.edit_form' => 'Edit', 'entity.node.delete_form' => 'Delete', ];
    foreach($links as $rout=>$text) {
      $url = Url::fromRoute($rout, array('node' => $nid));
      $link = Link::fromTextAndUrl(t($text), $url)->toRenderable();
      $link['#attributes'] = array('class' => array('button', 'button-action'));
      $markup .= render($link).' ';
    }

    $block = [
      '#type' => 'markup',
      '#markup' => $node->label(),
    ];
    return $block;
  }

}