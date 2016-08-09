<?php
namespace Drupal\file_entity_check;
use League\Flysystem\Filesystem;
class EntityCheck {
  public static function entityCheck($q1, &$context){
    $message = 'Checking File Entity Exist...';
    $results = array();
    foreach($q1 as $r)
         {
			 //if(!file_exists($r)) {
			 if(!$this->FileExistsException($r)) {
				 $results[] =$r;
			 }
			
		 }
	$context['message'] = $message;
    $context['results'] = $results;
  }
  public static function entityCheckFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message =\Drupal::translation()->formatPlural(
        count($results),
        'One post processed.', '@count File Not exist.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
    dsm ($results);
    
  }
}
