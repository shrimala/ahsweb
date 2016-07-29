<?php

/**
 * @file
 * Contains \Drupal\migrate_nd\Plugin\migrate\source\MediavNode.
 */

namespace Drupal\migrate_nd\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for media content.
 *
 * @MigrateSource(
 *   id = "mediav_node"
 * )
 */
class MediavNode extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    /**
     * An important point to note is that your query *must* return a single row
     * for each item to be imported. Here we might be tempted to add a join to
     * media_mg_media_topic_node in our query, to pull in the
     * relationships to our categories. Doing this would cause the query to
     * return multiple rows for a given node, once per related value, thus
     * processing the same node multiple times, each time with only one of the
     * multiple values that should be imported. To avoid that, we simply query
     * the base node data here, and pull in the relationships in prepareRow()
     * below.
     */
    $query = $this->select('migrate_nd_mdv_node', 'b')
                 ->fields('b', ['vbid', 'title','aid']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'vbid' => $this->t('Venue ID'),
      'title' => $this->t('Title of Venue'),
      'aid' => $this->t('Auther')
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'vbid' => [
        'type' => 'integer',
        'alias' => 'b',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
   return parent::prepareRow($row);
  }

}
