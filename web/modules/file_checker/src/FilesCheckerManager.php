<?php
 
/**
 * @file
 * Contains \Drupal\file_checker\FilesCheckerManager
 */
 
namespace Drupal\file_checker;

class FilesCheckerManager {
  public function setupBatches() {
    \Drupal::state()->set('file_checker.file_checker_status',1);
    $file_count = \Drupal::entityQuery('file')->count()->execute();
    \Drupal::state()->set('file_checker.batch_total',$file_count);
    $batch_size=100;
    $batches=ceil($file_count/$batch_size);

    for ($batch = 1; $batch <= $batches; $batch++) {
      $batchStart = 1 + (($batch-1)*($batch_size));
      $batchEnd = min(($batchStart+$batch_size),$file_count);
	    $fileIds = \Drupal::entityQuery('file')->range($batchStart,$batchEnd)->execute();
      $batch = array(
        'title' => t('Checking File Entity Exist...'),
        'operations' => array(
          array(
            '\Drupal\file_checker\FilesCheckBatch::check',
            array($fileIds)
          ),
        ),
        'finished' => '\Drupal\file_checker\FilesCheckBatch::finished',
      );
    batch_set($batch);
    sleep(1);
    }
    
  }

}
