<?php

namespace Drupal\file_checker\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file_checker\FileChecker;

/**
 * File Checker settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The File Checker service.
   */
  protected $fileChecker;

  /**
   * Constructs a File Checker settings form object.
   *
   * @param $file_checker
   *   The File Checker service.
   */
  public function __construct($file_checker) {
    $this->fileChecker = $file_checker;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_checker.file_checker')
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

    $form['last_status'] = array(
      '#markup' => "<p>" . $this->fileChecker->lastStatus() . "</p>",
    );

    $form['current_status'] = array(
      '#markup' => "<p>" . $this->fileChecker->currentStatus() . "</p>",
    );

    if ($this->fileChecker->isQueueEmpty()) {
      $form['check_once'] = array(
        '#type' => 'submit',
        '#value' => t('Check files once'),
        '#submit' => array('::checkFiles'),
      );
    }
    else {
      $form['cancel'] = array(
        '#type' => 'submit',
        '#value' => t('Cancel queued files'),
        '#submit' => array('::cancel'),
      );
    }

    $form['settings'] = [
      '#title' => t('File Checker settings'),
      '#type' => 'details',
      '#open' => TRUE,
    ];

    $form['settings']['run_by_cron'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Initiate file checking whenever cron runs'),
      '#default_value' => $config->get('run_by_cron'),
    );

    $form['settings']['frequency_limit'] = array(
      '#type' => 'select',
      '#title' => t('Do not initiate file checking more often than'),
      '#options' => array(
        '0' => 'No limit',
        '3600' => 'Once per  hour',
        '86400' => 'Once per  day',
        '604800' => 'Once per  week',
      ),
      '#default_value' => $config->get('frequency_limit'),
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
  public function checkFiles(array &$form, FormStateInterface $form_state) {
    $this->fileChecker->queueFiles();
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
    $this->fileChecker->cancel();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('file_checker.settings');
    $config->set('frequency_limit', $form_state->getValue('frequency_limit'))->save();
    $config->set('run_by_cron', $form_state->getValue('run_by_cron'))->save();
    parent::submitForm($form, $form_state);
  }

}
