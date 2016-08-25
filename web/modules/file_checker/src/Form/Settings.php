<?php
/**
 * Contains \Drupal\file_checker\Form\Settings.
 */
namespace Drupal\file_checker\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

use Drupal\Core\Datetime\DateFormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\ConfigFormBase;
/**
 * Contribute form
 */
class Settings extends ConfigFormBase {

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
    return 'file_checker_settings_form';
  }
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'file_checker.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
	$config = $this->config('file_checker.settings');
    $form['ok_btn'] = array(
      '#type' => 'submit',
      '#value' => t('Check files'),
    );
    global $base_url;
    $results_count=\Drupal::state()->get('file_checker.count');
      
    // Query to get log id based on last run and run type( cron or manually).
    $wid = db_query("SELECT wid FROM watchdog where type='file_checker_".\Drupal::state()->get('file_checker.run_by')."' order by wid desc limit 1")->fetchField();
     
    // Store data in variable for display total missing in $result_count, whole status in $result _status and last run timming in $last_run.
    // If dblog module enable and find the proper watchdog id then link is activate other wise deactivate.
    $results_status =  ($results_count>0 ? '<strong>Batch '.\Drupal::state()->get('file_checker.batch_pass').' of '.\Drupal::state()->get('file_checker.batch_total').' successful processed</strong> &emsp;&emsp;'.(($wid>0 && \Drupal::moduleHandler()->moduleExists('dblog')==TRUE)?'<a href="'.$base_url.'/admin/reports/dblog/event/'.$wid.'">'.$results_count.' file(s) Not exist.</a>':$results_count.' file(s) Not exist.'):'');
    $last_run=\Drupal::state()->get('file_checker.last_run');
    $status = '<p>' . ($last_run>0 ? $this->t('Last run: %time ago. &emsp;&emsp;&emsp;<strong>'.$results_status.'</strong>', array('%time' => $this->dateFormatter->formatTimeDiffSince($last_run))) : 'Last run: Never.') . '</p>';
      
    // Display the time, when last file checker run's and display no. of batch process run , total batch process and total no. of file missing.
    $form['status'] = array(
      '#markup' => $status,
    );
    $form['run_by_cron'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Check files when cron runs'),
    );
    $form['cron_time'] = array(
      '#type' => 'select',
      '#title' => t('Do not check files from cron more often than'),
      '#options' => array(
        '0' => 'No limit',
        '3600' => 'Once per  hour',
        '86400' => 'Once per  day',
        '604800' => 'Once per  week',
      ),
    ); 
    $form['save_config'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#submit' => array('::configuration_submit_function'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
 
public function validateForm(array &$form, FormStateInterface $form_state) {
    
  }
/**
* {@inheritdoc}
*/
public function submitForm(array &$form, FormStateInterface $form_state) {

    \Drupal::state()->set('file_checker.run_by','manually');
    \Drupal::state()->set('file_checker.count',0);  
    \Drupal::state()->set('file_checker.batch_pass',0);
  
    \Drupal::service('file_checker.files_checker_manager')->getFilesCheckerManagerValue();
    $time=REQUEST_TIME;
    \Drupal::state()->set('file_checker.last_run',$time);
    \Drupal::logger('file_checker_'.\Drupal::state()->get('file_checker.run_by'))->warning('@variable: '.\Drupal::state()->get('file_checker.result'), array('@variable' => 'Media Missing ', ));
    \Drupal::state()->set('file_checker.result','');
    
  }
  function configuration_submit_function(&$form, &$form_state) {
    // Save the cron configuration settings for file checker.
    if ($form_state->getValue('run_by_cron')==1) {
      \Drupal::service('config.factory')->getEditable('file_checker.frequency_limit')->set('frequency_limit', $form_state->getValue('cron_time'))->save();
      \Drupal::state()->set('file_checker.run_by_cron',$form_state->getValue('run_by_cron'));
    }
    else {
      \Drupal::state()->set('file_checker.frequency_limit','None');
      \Drupal::state()->set('file_checker.run_by_cron',0);
    }
    drupal_set_message('The configuration options have been saved.');
  }
}
?>
