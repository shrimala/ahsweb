<?php
require_once 'vendor/autoload.php';

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

namespace Drupal\file_entity_check;
class EntityCheck {
  public static function entityCheck($q1, &$context){
	  $adapter = new Local(__DIR__ . '/uploads');
$filesystem = new Filesystem($adapter);

    $message = 'Checking File Entity Exist...';
    $results = array();
    foreach($q1 as $r)
         {
			 if(!$filesystem->has($r)) {
				 $results[] =$r. "-------- Not Exist";
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
