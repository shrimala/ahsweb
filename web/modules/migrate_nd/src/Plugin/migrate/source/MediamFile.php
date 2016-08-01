<?php

/**
 * @file
 * Contains \Drupal\migrate_nd\Plugin\migrate\source\MediamFile.
 */

namespace Drupal\migrate_nd\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for sessions content.
 *
 * @MigrateSource(
 *   id = "mediam_file"
 * )
 */
class MediamFile extends SqlBase {

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
    $query = $this->select('migrate_nd_mdf_node', 'b')
                 ->fields('b', ['fid', 'uid','filename','filepath','filemime','status','timestamp','uri']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'fid' => $this->t('File ID'),
      'uid' => $this->t('The {users}.uid who added the file. If set to 0, this file was added by an anonymous user.'),
      'filename' => $this->t('File name'),
      'filepath' => $this->t('File path'),
      'filemime' => $this->t('File Mime Type'),
      'status' => $this->t('The published status of a file.'),
      'timestamp' => $this->t('The time that the file was added.'),
      'uri' => $this->t('The time that the file was added.'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'filename' => [
        'type' => 'string',
        'alias' => 'b',
      ],
      'filepath' => [
        'type' => 'string',
        'alias' => 'b',
      ],
      'fid' => [
        'type' => 'integer',
        'alias' => 'b',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
   // Row is a now a class with helpful methods.
    // Set the complete external path to the image.
    $filename=$row->getSourceProperty('filename');
    $filename=str_replace("/","",$filename);
    $filepath=$row->getSourceProperty('filepath');
    $filepath=$filepath.$filename;
    $row->setSourceProperty('filename',$filename);
    $row->setSourceProperty('filepath',$filepath);
    return parent::prepareRow($row);
  }
}
