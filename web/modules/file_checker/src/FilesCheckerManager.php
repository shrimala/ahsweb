<?php
 
/**
 * @file
 * Contains \Drupal\file_checker\FilesCheckerManager
 */
 
namespace Drupal\file_checker;

class FilesCheckerManager {
  public function getFilesCheckerManagerValue() {
	\Drupal::state()->set('file_checker.file_checker_status',1);
	$q = \Drupal::entityQuery('file');
    $uri_count = $q->count()->execute();
    $loop_run=ceil($uri_count/100);
    $x=1;
    \Drupal::state()->set('file_checker.batch_total',ceil($uri_count/100));
	$first=1;
	$last=100;
    while($loop_run>=$x)
    {
		$q1 = \Drupal::entityQuery('file');
        $r2 = $q1->range(($first*$x),($last*$x))->execute();
        $file=\Drupal::entityTypeManager()->getStorage('file')->loadMultiple($r2);
		$batch = array(
      'title' => t('Checking File Entity Exist...'),
      'operations' => array(
        array(
          '\Drupal\file_checker\FilesCheckBatch::check',
          array($file)
          ),
        ),
      'finished' => '\Drupal\file_checker\FilesCheckBatch::entityCheckFinishedCallback',
    );
    batch_set($batch);
    $first=$last+1;
    $last=$last+100;
    $x=$x+1;
    }
    
  }
 
}
