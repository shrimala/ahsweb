<?php
namespace Drupal\file_checker;
class FilesCheckBatch {
  public static function check($q1, &$context){
	$message = t('Checking files exist...');
    $results = array();
    foreach($q1 as $r)
         {
			 if(substr($r,0,1)=="p") {
				 if(!file_exists($r)) {
					 $results[] =$r;
				 }
			 
			 }
			 else {
				 if(!file_exists($r)) {
					 $results[] =$r;
				 }
			 }
		 }
	$context['message'] = $message;
    $context['results'] = $results;
  }
  public static function entityCheckFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $batch_pass=\Drupal::state()->get('file_checker.batch_pass') + 1;
      \Drupal::state()->set('file_checker.batch_pass',$batch_pass);
    }
    else {
      $message = t('Finished with an error.');
    }
    
    if( sizeof($results)>0) {
		//$run_by = db_query("SELECT run_by FROM file_check_config")->fetchField();
		$run_by = \Drupal::state()->get('file_checker.run_by');
		$count=\Drupal::state()->get('file_checker.count');
		$count=$count + sizeof($results);
		\Drupal::state()->set('file_checker.count',$count);
		$results_string="";
		$results_string=\Drupal::state()->get('file_checker.result');
		$results_string = $results_string."<pre>".implode(",\n", $results)."</pre>";
		\Drupal::state()->set('file_checker.result',$results_string);
	}
    
  }
}
