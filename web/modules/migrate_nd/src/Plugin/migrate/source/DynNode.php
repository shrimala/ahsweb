<?php

/**
 * @file
 * Contains \Drupal\migrate_nd\Plugin\migrate\source\DynNode.
 */

namespace Drupal\migrate_nd\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for dyn content.
 *
 * @MigrateSource(
 *   id = "dyn_node"
 * )
 */
class DynNode extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    /**
     * An important point to note is that your query *must* return a single row
     * for each item to be imported. Here we might be tempted to add a join to
     * migrate_nd_dyn_topic_node in our query, to pull in the
     * relationships to our categories. Doing this would cause the query to
     * return multiple rows for a given node, once per related value, thus
     * processing the same node multiple times, each time with only one of the
     * multiple values that should be imported. To avoid that, we simply query
     * the base node data here, and pull in the relationships in prepareRow()
     * below.
     */
    $query = $this->select('migrate_nd_dyn_node', 'b')
                 ->fields('b', ['bbid', 'title', 'abstract','article','aid','bodyformat','Original']);
    $query->addExpression('UNIX_TIMESTAMP(dt_created)', 'dt_created');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'bbid' => $this->t('Ssd ID'),
      'title' => $this->t('Title of ssd'),
      'dt_created' => $this->t('Creation date of ssd'),
      'abstract' => $this->t('Abstract for this ssd'),
      'article' => $this->t('Article of ssd'),
      'aid' => $this->t('Account ID of the author'),
      'bodyformat' => $this->t('Text format i.e html or full html'),
      'Original' => $this->t('unproccessd data'),
      'terms' => $this->t('Applicable styles'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'bbid' => [
        'type' => 'integer',
        'alias' => 'b',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    /**
     * As explained above, we need to pull the style relationships into our
     * source row here, as an array of 'style' values (the unique ID for
     * the dyn_term migration).
     */
    $terms = $this->select('migrate_nd_dyn_topic_node', 'bt')
                 ->fields('bt', ['style'])
      ->condition('bbid', $row->getSourceProperty('bbid'))
      ->execute()
      ->fetchCol();
    $row->setSourceProperty('terms', $terms);

    return parent::prepareRow($row);
  }

}
