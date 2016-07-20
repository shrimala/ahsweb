<?php

/**
 * @file
 * Contains \Drupal\migrate_nd\Plugin\migrate\source\MediamMedia.
 */

namespace Drupal\migrate_nd\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for sessions content.
 *
 * @MigrateSource(
 *   id = "mediam_media"
 * )
 */
class MediamMedia extends SqlBase {

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
    $query = $this->select('migrate_nd_mdm_node', 'b')
                 ->fields('b', ['mebid', 'title','aid','uril','filename']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'mebid' => $this->t('Media audio ID'),
      'title' => $this->t('Title of Media audio'),
      'aid' => $this->t('Auther id'),
      'uril' => $this->t('url of file'),
      'filename' => $this->t('file name'),
      'fid' => $this->t('file id'),
      'sbid' => $this->t('session id'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'mebid' => [
        'type' => 'integer',
        'alias' => 'b',
      ],
      'filename' => [
        'type' => 'string',
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
   //$filename=str_replace("/","",$filename);
   
   
    $j=0;
     //$q3 = db_query("SELECT destid1 FROM migrate_map_mediam_file  WHERE sourceid1 ='". $filename ."'");
      $q3 = db_query("SELECT fid FROM file_managed  WHERE filename ='". $filename ."'");
          foreach($q3 as $r3)
          {
			  $fileid[$j]=$r3->fid;
			  $j=$j+1;
		  }
   /**$session_id = $this->select('migrate_nd_mdstemp_node', 'bt')
                 ->fields('bt', ['sbid'])
      ->condition('meytbid', $row->getSourceProperty('mebid'))
      ->execute()
      ->fetchCol();
   
   /**$q1 = db_query("select a.sbid,c.mebid from migrate_nd_mdstemp_node a, migrate_nd_sestemp_node b, migrate_nd_mdm_node c where  c.filename=b.Recordings  and b.title=a.title  and  c.mebid=".$row->getSourceProperty('mebid') );
        foreach($q1 as $r)
         {
		   $session_id[$i]=$r->sbid;
		   $i=$i+1;
		 }   
*/
   
   //$row->setSourceProperty('sbid',$session_id);
   $row->setSourceProperty('filename',$filename);
   $row->setSourceProperty('fid',$fileid);
    return parent::prepareRow($row);
  }
}
