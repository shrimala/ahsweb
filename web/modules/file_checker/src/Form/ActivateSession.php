<?php
/**
 * Contains \Drupal\file_checker\Form\ActivateSession.
 */
namespace Drupal\file_checker\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\CronInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Contribute form
 */
class ActivateSession extends FormBase {
	 /**
   * Stores the state storage service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The cron service.
   *
   * @var \Drupal\Core\CronInterface
   */
  protected $cron;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   */
  protected $moduleHandler;

  /**
   * Constructs a CronForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state key value store.
   * @param \Drupal\Core\CronInterface $cron
   *   The cron service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, StateInterface $state, CronInterface $cron, DateFormatterInterface $date_formatter, ModuleHandlerInterface $module_handler) {
    $this->state = $state;
    $this->cron = $cron;
    $this->dateFormatter = $date_formatter;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('state'),
      $container->get('cron'),
      $container->get('date.formatter'),
      $container->get('module_handler')
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
    $result=db_query("SELECT last_run FROM file_check_config")->fetchField();
    //$status = '<p>' . $this->t('Last run: %time ago.', array('%time' => $this->dateFormatter->formatTimeDiffSince($this->state->get('system.cron_last')))) . '</p>';
    $status = '<p>' . ($result>0 ? $this->t('Last run: %time ago.', array('%time' => $this->dateFormatter->formatTimeDiffSince($result))) : 'Last run: Never Check') . '</p>';
    $form['status'] = array(
      '#markup' => $status,
    );
    $form['run_by_cron'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Run by cron'),
    );
    $form['corn_time'] = array(
      '#type' => 'select',
      '#title' => t('Do not run more often than'),
      '#options' => array(
        'No limit' => 'No limit',
        '1 hour' => '1 hour',
        '1 day' => '1 day',
        '1 week' => '1 week',
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
  db_update('file_check_config')
    ->fields(array(
      'last_run' => REQUEST_TIME,
    ))
  ->execute();

		 $q = db_query("SELECT count(uri) as uri  FROM file_managed");
		  $r1 = $q->fetchAssoc();
		  $uri_count=$r1['uri'];
		  $first=0;
		  $last=100;
		 while($uri_count>$first)
		 {
		 $q1 = db_query("SELECT fid,filename,uri  FROM file_managed where fid between ".$first ." and ".$last);
         $result=array();
        foreach($q1 as $r)
         {
			 $result[$r->uri]=$r->uri;
			 //$result[$r->filename]=$r->filename;
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
  }
  function configuration_submit_function(&$form, &$form_state) {
	//drupal_set_message($this->dateFormatter->formatTimeDiffSince(REQUEST_TIME -(7*24*3600)));
    
  // This would be executed.
    if ($form_state->getValue('run_by_cron')==1) {
      db_update('file_check_config')
      ->fields(array(
        'time_duration' => $form_state->getValue('corn_time'),
        'run_by_cron' => $form_state->getValue('run_by_cron'),
    ))
    ->execute();
    drupal_set_message('Configuration saved with File Checker run with cron but Do not run more often than ' . $form_state->getValue('corn_time'));
    }
    else
    {
      db_update('file_check_config')
      ->fields(array(
        'time_duration' => 'None',
        'run_by_cron' => $form_state->getValue('run_by_cron'),
    ))
    ->execute();
    drupal_set_message('Configuration saved with File Checker not Run with cron');
    }
  }
}
?>
