<?php
 
/**
 * @file
 * Contains \Drupal\file_checker\FilesCheckerManager
 */
 
namespace Drupal\file_checker;
 
class FilesCheckerManager {
  public function getFilesCheckerManagerValue() {
	$q = \Drupal::entityQuery('file');
    $r1 = $q->count()->execute();
    $uri_count=$r1;
    \Drupal::state()->set('file_checker.batch_total',ceil($uri_count/100));
	$first=1;
	$last=100;
	while($uri_count>$first) {
	  $result=array();
      $x=$first;
      while ($x<=$last) {
		$file=\Drupal::entityTypeManager()->getStorage('file')->load($x);
		$result[$file->uri->value]=$file->uri->value;
		$x=$x+1;
      }
      $batch = array(
      'title' => t('Checking File Entity Exist...'),
      'operations' => array(
        array(
          '\Drupal\file_checker\FilesCheckBatch::check',
          array($result)
          ),
        ),
      'finished' => '\Drupal\file_checker\FilesCheckBatch::entityCheckFinishedCallback',
    );
    batch_set($batch);
    $first=$last+1;
    $last=$last+100;
    }
  }
 
}
