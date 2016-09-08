<?php

namespace Drupal\file_checker\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file_checker\BulkFileChecking;

/**
 * File Checker settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The bulk file checking service.
   */
  protected $bulkFileChecking;

  /**
   * Constructs a File Checker settings form object.
   *
   * @param $bulk_file_checking
   *   The bulk file checking service.
   */
  public function __construct($bulk_file_checking) {
    $this->bulkFileChecking = $bulk_file_checking;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_checker.bulk_file_checking')
    );
  }

  /**
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('file_checker.settings');

    $description = t('File Checker verifies that files managed by Drupal actually exist at the location where Drupal believes they are.');
    $form['description'] = array(
      '#markup' => '<p>' . $description . '</p>',
    );

    $form['status'] = array(
      '#type' => 'container',
    );

    $form['status']['missing'] = $this->bulkFileChecking->missingStatus();
    $form['status']['missing']['#prefix'] = '<p>';

    $form['status']['last_status'] = array(
      '#markup' => $this->bulkFileChecking->lastStatus(),
      '#prefix' => "&nbsp;",
      '#suffix' => "</p>",
    );

    $form['current_status'] = array(
      '#markup' => "<p>" . $this->bulkFileChecking->backgroundStatus() . "</p>",
    );

    if (!$this->bulkFileChecking->hasBeenRequested()) {
      $form['check_now'] = array(
        '#type' => 'submit',
        '#value' => t('Check files now'),
        '#submit' => array('::checkNow'),
      );
    }
    else {
      $form['cancel'] = array(
        '#type' => 'submit',
        '#value' => t('Cancel file checking'),
        '#submit' => array('::cancel'),
      );
    }

    $form['settings'] = [
      '#title' => t('File Checker settings'),
      '#type' => 'details',
      '#open' => TRUE,
    ];

    $form['settings']['check_on_change'] = array(
      '#type' => 'select',
      '#title' => t('Check file when stored location changes?'),
      '#options' => array(
        'no' => 'No',
        'immediately' => 'Immediately',
        'later' => 'Later',
      ),
      '#default_value' => $config->get('check_on_change'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Initiate a file checking run.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function checkNow(array &$form, FormStateInterface $form_state) {
    $batch = array(
      'title' => t('Checking files'),
      'init_message' => t('File checking is under way.'),
      'progress_message' => t('Checking files: batch @current.'),
      'operations' => array(
        array('file_checker_execute_in_ui', array()),
      ),
    );
    batch_set($batch);
  }

  /**
   * Cancel the queued file checking run.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function cancel(array &$form, FormStateInterface $form_state) {
    $this->bulkFileChecking->cancel();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('file_checker.settings');
    $config->set('check_on_change', $form_state->getValue('check_on_change'))->save();
    parent::submitForm($form, $form_state);
  }

}
