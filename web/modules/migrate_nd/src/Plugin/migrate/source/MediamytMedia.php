<?php

/**
 * @file
 * Contains \Drupal\migrate_nd\Plugin\migrate\source\MediamytMedia.
 */

namespace Drupal\migrate_nd\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for sessions content.
 *
 * @MigrateSource(
 *   id = "mediamyt_media"
 * )
 */
class MediamytMedia extends SqlBase {

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
    $query = $this->select('migrate_nd_mdmyt_node', 'b')
                 ->fields('b', ['meytbid', 'title','aid','uril','filename']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'meytbid' => $this->t('Media youtube ID'),
      'title' => $this->t('Title of Media title'),
      'aid' => $this->t('Auther id'),
      'uril' => $this->t('url of file'),
      'filename' => $this->t('file name'),
      'sbid' => $this->t('session id'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'meytbid' => [
        'type' => 'integer',
        'alias' => 'b',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
     $filepath = $row->getSourceProperty('uril');
     $filepath="https://www.youtube.com/watch?v=".$filepath;
    // We update the file path from the row within the local path in our computer.
    $row->setSourceProperty('uril', $filepath);
    // Retrieve the filename as our process plugin require it.
    $file_name = $row->getSourceProperty('filename');
    // Set the row property "file name".
    
    $q1 = db_query("select a.sbid from migrate_nd_mdstemp_node a, migrate_nd_sestemp_node b, migrate_nd_mdmyt_node c where c.filename=b.Recordings and b.title=a.title and c.meytbid=".addslashes($row->getSourceProperty('meytbid')) );
    foreach($q1 as $r)
      {
		$session_id=$r->sbid;
	  }   
      
    $row->setSourceProperty('sbid',$session_id);
    $row->setSourceProperty('filename', $file_name);
    return parent::prepareRow($row);
  }

}
