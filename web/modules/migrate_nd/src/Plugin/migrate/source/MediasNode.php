<?php

/**
 * @file
 * Contains \Drupal\migrate_nd\Plugin\migrate\source\MediasNode.
 */

namespace Drupal\migrate_nd\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for sessions content.
 *
 * @MigrateSource(
 *   id = "medias_node"
 * )
 */
class MediasNode extends SqlBase {

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
    $query = $this->select('migrate_nd_mdstemp_node', 'b')
                 ->fields('b', ['sbid', 'title','dt_session','descrip','aid','mbid','meytbid','eventid','old_id','type','body_summery','field_clip','field_old_catalog','field_restricted','field_admin_tags']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'sbid' => $this->t('Session ID'),
      'title' => $this->t('Title of Session'),
      'dt_session' => $this->t('Session date'),
      'descrip' => $this->t('Session description'),
      'aid' => $this->t('Autherid'),
      'mbid' => $this->t('Teacher id'),
      'meytbid' => $this->t('Media youtube id'),
      'mebid' =>  $this->t('Media Audio id'),
      'old_id' =>  $this->t('Teachings tags reference id'),
      'type' => $this->t('Session type'),
      'body_summery' => $this->t('Summary of the body'),
      'field_clip' => $this->t('Youtube clip avalable or not'),
      'field_old_catalog' => $this->t('Body original data without format'),
      'field_restricted' => $this->t('Restricted'),
      'field_admin_tags' => $this->t('Admin tags reference id'),
      'terms' => $this->t('Applicable styles'),
      'adminbid' => $this->t('Admin tags Applicable styles'),
      'sessionbid' => $this->t('session type tags Applicable styles'),
      'ebid' => $this->t('Event ID'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'sbid' => [
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
     //$fields = array('bbid', 'style');
    $obj = db_query('SELECT distinct title, old_id, field_restricted, field_clip,eventid,sbid FROM migrate_nd_mdstemp_node WHERE sbid='.addslashes($row->getSourceProperty('sbid')));
    $i=0;
    $i1=0;
    $j=0;
    $j1=0;
    $restriction=0;
    $fieldclip=0;
    foreach($obj as $obj1)
      {
	    $q1 = db_query("SELECT distinct recordings FROM migrate_nd_sestemp_node WHERE title='".addslashes($obj1->title)."' and sbid='". $obj1->sbid."'");
        foreach($q1 as $r)
         {
		   $data1[$i]=$r->recordings;
		   $i=$i+1;
		 }
		$q10 = db_query("SELECT field_leader FROM sessiondata WHERE title='".addslashes($obj1->title)."' and SL='". $obj1->sbid."'");
        foreach($q10 as $r10)
         {
		   $teachername=$r10->field_leader;
		   
		 }
		if ($obj1->field_restricted==1) {
			$restriction=$obj1->field_restricted;
		}
		
		if ($obj1->field_clip==1) {
			$fieldclip=$obj1->field_clip;
		}
		$eventdata = $this->select('migrate_nd_mde_node', 'bt')
                 ->fields('bt', ['ebid'])
      ->condition('ebid', $obj1->eventid)
      ->execute()
      ->fetchCol();
	  }
	  
	  $data1count=count($data1);
      for ($xyz=0;$xyz<$data1count;$xyz++)
	  {
		  /**$q2 = db_query("SELECT distinct  mid FROM media_field_data WHERE name =(SELECT distinct title FROM migrate_nd_mdmyt_node WHERE filename='".addslashes($data1[$xyz])."')");*/
		  $q2=db_query("SELECT distinct  mid FROM media_field_data WHERE mid =(select destid1 from migrate_map_mediamyt_media where sourceid1=(SELECT  meytbid FROM migrate_nd_mdmyt_node WHERE filename='" . addslashes($data1[$xyz]) . "'))");
		  
          foreach($q2 as $r2)
          {
			  $meytbidref[$j]=$r2->mid;
			  $j=$j+1;
		  }
          
          $q3 = db_query("SELECT distinct mid FROM media_field_data WHERE name =(SELECT distinct title FROM migrate_nd_mdm_node WHERE filename='".addslashes($data1[$xyz])."')");
          foreach($q3 as $r3)
          {
			  $meytbidref[$j]=$r3->mid;
			  $j=$j+1;
		  }
      }

	  $teachername1=explode(',',$teachername);
	  $t1=count($teachername1);
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
	  
	  //-------------------------------
    $terms = $this->select('migrate_nd_dyn_topic_node', 'bt')
                 ->fields('bt', ['style'])
      ->condition('bbid', $obj1->old_id)
      ->execute()
      ->fetchCol();
    //-------------------------  
	/**$q1 = db_query("SELECT field_admin_tags FROM migrate_nd_mds_node WHERE sbid='".$row->getSourceProperty('sbid')."'");
    foreach($q1 as $r1) {
	  $admintags=$r1->field_admin_tags;   
	}
	$admintags1=explode(',',$admintags);*/
	
	
	$admintags1 = $this->select('migrate_nd_admin_topic_node', 'bt')
                 ->fields('bt', ['style'])->distinct()
      ->condition('adminbid', $row->getSourceProperty('sbid'))
      ->execute()
      ->fetchCol();
	//-----------------------------
	$sessiontags1 = $this->select('migrate_nd_session_topic_node', 'bt')
                 ->fields('bt', ['style'])
      ->condition('sessionbid', $row->getSourceProperty('sbid'))
      ->execute()
      ->fetchCol();
    //-------------------------------
    $row->setSourceProperty('mbid',$data);
    $row->setSourceProperty('ebid',$eventdata);  
    $row->setSourceProperty('meytbid', $meytbidref);
    $row->setSourceProperty('mebid',$mebidref);
    $row->setSourceProperty('terms', $terms);
    $row->setSourceProperty('adminbid', $admintags1);
    $row->setSourceProperty('sessionbid', $sessiontags1);
    $row->setSourceProperty('field_restricted', $restriction);
    $row->setSourceProperty('field_clip', $fieldclip);
    return parent::prepareRow($row);
  }

}
