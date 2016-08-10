<?php
namespace Drupal\file_entity_check;
class EntityCheck {
  public static function entityCheck($q1, &$context){
	$platform_variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);
    $DROPBOX_TOKEN = $platform_variables["DROPBOX_TOKEN"];	  
    $message = 'Checking File Entity Exist...';
    $results = array();
    foreach($q1 as $r)
         {
			 if(substr($r,0,1)=="p") {
				 if(!file_exists($r)) {
					 $results[] =$r ."-----Not Exists";
				 }
			 
			 }
			 else {
				 if(!file_exists("https://{$DROPBOX_TOKEN}@dropbox.com/".substr($r,20))) {
					 $results[] ="https://{$DROPBOX_TOKEN}@dropbox.com/".substr($r,20) ."-----Not Exists";
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
