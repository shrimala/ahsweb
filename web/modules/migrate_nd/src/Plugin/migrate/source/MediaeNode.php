<?php

/**
 * @file
 * Contains \Drupal\migrate_nd\Plugin\migrate\source\MediaeNode.
 */

namespace Drupal\migrate_nd\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for sessions content.
 *
 * @MigrateSource(
 *   id = "mediae_node"
 * )
 */
class MediaeNode extends SqlBase {

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
    $query = $this->select('migrate_nd_mde_node', 'b')
                 ->fields('b', ['ebid', 'title','dt_event','eventbody','aid','sbid','field_leader','field_venue','field_event_tags','eventsummary']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'ebid' => $this->t('Event ID'),
      'title' => $this->t('Title of Event'),
      'dt_event' => $this->t('Event date'),
      'eventbody' => $this->t('Body of Event'),
      'aid' => $this->t('Auther'),
      'sbid' => $this->t('session id'),
      'vbid' => $this->t('Venue'),
      'mbid' => $this->t('Teacher id'),
      'eventag' => $this->t('Event tags'),
      'eventsumary' => $this->t('summary of Event'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'ebid' => [
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
     * the media_term migration).
     */
    
    $sbidr = $this->select('migrate_nd_mdstemp_node', 'e')
                 ->fields('e', ['sbid'])
                 ->condition('eventid', $row->getSourceProperty('ebid'))
				 ->execute()
				 ->fetchCol();
    
    
    $q10 = db_query("SELECT field_leader FROM event WHERE id='".addslashes($row->getSourceProperty('ebid'))."'");
    foreach($q10 as $r10)
         {
		   $teachername=$r10->field_leader;
		   
		 }
	    
    $teachername1=explode(',',$teachername);
	$t1=count($teachername1);
	$i1=0;
	for ($xyz1=0;$xyz1<$t1;$xyz1++)
	  {
	  $q101 = db_query("SELECT mbid FROM migrate_nd_md_node WHERE title ='".addslashes($teachername1[$xyz1])."'");
        foreach($q101 as $r101)
         {
		   $data[$i1]=$r101->mbid;
		   $i1=$i1+1;
		   break;
		 }
	  }
     
     
    $q1 = db_query("SELECT field_venue FROM event WHERE id='".addslashes($row->getSourceProperty('ebid'))."'");
    foreach($q1 as $r1)
         {
		   $venue=$r1->field_venue;
		   
		 }
	    
    $venue1=explode(',',$venue);
	$t1=count($venue1);
	$i1=0;
	for ($xyz=0;$xyz<$t1;$xyz++)
	  {
		  
	  $q102 = db_query("SELECT vbid FROM migrate_nd_mdv_node WHERE title ='".addslashes($venue1[$xyz])."'");
        foreach($q102 as $r102)
         {
		   $d1[$i1]=$r102->vbid;
		   $i1=$i1+1;
		   break;
		 }
	  }
	//-------------------------  
	$q1 = db_query("SELECT field_event_tags FROM migrate_nd_mde_node WHERE ebid='".addslashes($row->getSourceProperty('ebid'))."'");
    foreach($q1 as $r1) {
	  $eventtags=$r1->field_event_tags;   
	}
	$eventtags1=explode(',',$eventtags);
    
    $row->setSourceProperty('mbid',$data);  
    $row->setSourceProperty('sbid', $sbidr);
    $row->setSourceProperty('vbid',$d1);    
    $row->setSourceProperty('eventag',$eventtags1);
    return parent::prepareRow($row);
  }

}
