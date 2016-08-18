<?php
/**
 * Contains \Drupal\file_checker\Form\ActivateSession.
 */
namespace Drupal\file_checker\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

use Drupal\Core\Datetime\DateFormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Contribute form
 */
class ActivateSession extends FormBase {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a CronForm object.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(DateFormatterInterface $date_formatter) {
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter')
    );
  }

  public function getFormId() {
    return 'file_checker_activatesession_form';
  }
  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['ok_btn'] = array(
      '#type' => 'submit',
      '#value' => t('Check files'),
    );
    global $base_url;
    $results_count=\Drupal::state()->get('file_checker.count');
    $wid = db_query("SELECT wid FROM watchdog where timestamp='".\Drupal::state()->get('file_checker.last_run')."' and type='file_checker_".\Drupal::state()->get('file_checker.run_by')."'")->fetchField();
    $results_status =  ($results_count>0 ? '<a href="'.$base_url.'/admin/reports/dblog/event/'.$wid.'">'.$results_count.' file(s) Not exist.</a>':'');
    $result=\Drupal::state()->get('file_checker.last_run');
    $status = '<p>' . ($result>0 ? $this->t('Last run: %time ago. &emsp;&emsp;&emsp;<strong>'.$results_status.'</strong>', array('%time' => $this->dateFormatter->formatTimeDiffSince($result))) : 'Last run: Never.') . '</p>';
    $form['status'] = array(
      '#markup' => $status,
    );
    $form['run_by_cron'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Check files when cron runs'),
      //'#value' => (\Drupal::state()->get('file_checker.run_by_cron')==1?1:0),
    );
    $form['corn_time'] = array(
      '#type' => 'select',
      '#title' => t('Do not check files from cron more often than'),
      '#options' => array(
        'No limit' => 'No limit',
        '1 hour' => 'Once per  hour',
        '1 day' => 'Once per  day',
        '1 week' => 'Once per  week',
      ),
    ); 
    $form['save_config'] = array(
      '#type' => 'submit',
      '#value' => t('Save configuration'),
      '#submit' => array('::configuration_submit_function'),
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
  \Drupal::state()->set('file_checker.last_run',REQUEST_TIME);
  \Drupal::state()->set('file_checker.run_by','manually');
  \Drupal::state()->set('file_checker.count',0);
  $q = db_query("SELECT count(uri) as uri  FROM file_managed");
  $r1 = $q->fetchAssoc();
  $uri_count=$r1['uri'];
  $first=0;
  $last=100;
  while($uri_count>$first) {
	$q1 = db_query("SELECT fid,filename,uri  FROM file_managed where fid between ".$first ." and ".$last);
    $result=array();
    foreach($q1 as $r) {
      $result[$r->uri]=$r->uri;
    }
	$batch = array(
      'title' => t('Checking File Entity Exist...'),
      'operations' => array(
        array(
          '\Drupal\file_checker\EntityCheck::entityCheck',
          array($result)
        ),
      ),
      'finished' => '\Drupal\file_checker\EntityCheck::entityCheckFinishedCallback',
    );
    batch_set($batch);
    $first=$last+1;
    $last=$last+100;
    }
    \Drupal::logger('file_checker_'.\Drupal::state()->get('file_checker.run_by'))->notice('@variable: '.\Drupal::state()->get('file_checker.result'), array('@variable' => 'Media Missing ', ));
  }
  function configuration_submit_function(&$form, &$form_state) {
    // This would be executed.
    if ($form_state->getValue('run_by_cron')==1) {
      \Drupal::state()->set('file_checker.time_duration',$form_state->getValue('corn_time'));
      \Drupal::state()->set('file_checker.run_by_cron',$form_state->getValue('run_by_cron'));
      drupal_set_message('Configuration saved with File Checker run with cron but Do not run more often than ' . $form_state->getValue('corn_time'));
    }
    else {
      \Drupal::state()->set('file_checker.time_duration','None');
      \Drupal::state()->set('file_checker.run_by_cron',$form_state->getValue('run_by_cron'));
      drupal_set_message('Configuration saved with File Checker not Check files when cron runs');
    }    
  }
}
?>
