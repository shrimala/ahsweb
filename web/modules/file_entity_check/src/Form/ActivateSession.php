<?php
/**
 * Contains \Drupal\file_entity_check\Form\ActivateSession.
 */
namespace Drupal\file_entity_check\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
/**
 * Contribute form
 */
class ActivateSession extends FormBase {
 /**
 * {@inheritdoc}
 */
  public function getFormId() {
    return 'file_entity_check_activatesession_form';
  }
  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['ok_btn'] = array(
      '#type' => 'submit',
      '#value' => t('File Entity Existenz Check'),
    );
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
 
public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate video URL.
  }
/**
* {@inheritdoc}
*/
public function submitForm(array &$form, FormStateInterface $form_state) {
  // Display result.
  $q1 = db_query("SELECT fid,filename,uri  FROM file_managed WHERE filemime='audio/mpeg'");
  
        /**foreach($q1 as $r)
         {
			 if(!file_exists($r->uri)) {
				 drupal_set_message("Fid = ".$r->fid." -------- File Name = ".$r->filename . "-------- Not Exist");
				 $i=$i+1;
			 }
			 
		 }*/
	$batch = array(
      'title' => t('Checking File Entity Exist...'),
      'operations' => array(
        array(
          '\Drupal\file_entity_check\EntityCheck::entityCheck',
          array($q1)
        ),
      ),
      'finished' => '\Drupal\file_entity_check\EntityCheck::entityCheckFinishedCallback',
    );
    batch_set($batch);
  }
}
?>
