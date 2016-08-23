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
    $wid = db_query("SELECT wid FROM watchdog where timestamp='".\Drupal::state()->get('file_checker.last_run')."' and type='file_checker_".\Drupal::state()->get('file_checker.run_by')."'")->fetchField();
    $results_status =  ($results_count>0 ? '<strong>Batch '.\Drupal::state()->get('file_checker.batch_pass').' of '.\Drupal::state()->get('file_checker.batch_total').' successful processed</strong> &emsp;&emsp;<a href="'.$base_url.'/admin/reports/dblog/event/'.$wid.'">'.$results_count.' file(s) Not exist.</a>':'');
    
    $result=\Drupal::state()->get('file_checker.last_run');
    $status = '<p>' . ($result>0 ? $this->t('Last run: %time ago. &emsp;&emsp;&emsp;<strong>'.$results_status.'</strong>', array('%time' => $this->dateFormatter->formatTimeDiffSince($result))) : 'Last run: Never.') . '</p>';
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

  \Drupal::state()->set('file_checker.run_by','manually');
  \Drupal::state()->set('file_checker.count',0);  
  \Drupal::state()->set('file_checker.batch_pass',0);
  
  \Drupal::service('file_checker.files_checker_manager')->getFilesCheckerManagerValue();

  \Drupal::state()->set('file_checker.last_run',REQUEST_TIME);
  \Drupal::logger('file_checker_'.\Drupal::state()->get('file_checker.run_by'))->warning('@variable: '.\Drupal::state()->get('file_checker.result'), array('@variable' => 'Media Missing ', ));
  \Drupal::state()->set('file_checker.result','');
  }
  function configuration_submit_function(&$form, &$form_state) {
    // This would be executed.
    if ($form_state->getValue('run_by_cron')==1) {
      \Drupal::service('config.factory')->getEditable('file_checker.frequency_limit')->set('frequency_limit', $form_state->getValue('cron_time'))->save();
      \Drupal::state()->set('file_checker.run_by_cron',$form_state->getValue('run_by_cron'));
      drupal_set_message(($form_state->getValue('cron_time')>0?'Configuration options saved. Check files when cron runs, but do not run more often than: ' . $form_state->getValue('cron_time'). ' seconds.':'Configuration options saved. Check files any time.'));
    }
    else {
      \Drupal::state()->set('file_checker.frequency_limit','None');
      \Drupal::state()->set('file_checker.run_by_cron',0);
      drupal_set_message('Configuration options saved. Do not check files when cron runs.');
    }    
  }
}
?>
