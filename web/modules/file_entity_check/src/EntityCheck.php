<?php
namespace Drupal\file_entity_check;
class FileEntity {
  public static function entityCheck($q1, &$context){
	  drupal_set_message("Arit Kumar Nath fro entityCheck Function");
    $message = 'Checking File Entity Exist...';
    $results = array();
    foreach($q1 as $r)
         {
			 if(!file_exists($r->uri)) {
				 $context['message'] =$r->uri. "-------- Not Exist";
			 }
			 
		 }
    $context['results'] = $results;
    drupal_set_message("Arit Kumar Nath fro entityCheck Function2");
  }
  function entityCheckFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One post processed.', '@count posts processed.'
      );
      $message=$message . "Message fire successfully.";
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
    drupal_set_message("Arit Kumar Nath fro entityCheckFinishedCallback Function");
  }
}
