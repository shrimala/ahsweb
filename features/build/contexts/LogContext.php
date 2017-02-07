<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Uses hooks to monitor Drupal errors and warnings displayed on the page.
 */

class LogContext extends RawMinkContext implements SnippetAcceptingContext {

  /**
   * Whether logging output is wanted
   */
  private $log;



  /**
   * @BeforeScenario @log
   */
  public function setLog(BeforeScenarioScope $scope)
  {
    $this->log = true;
  }

  /**
   * @AfterScenario @log
   */
  public function unsetLog(AfterScenarioScope $scope)
  {
    $this->log = false;
  }

  /**
   * Log HTML of all steps.
   *
   * @AfterStep
   */
  public function logHTML($event) {
    /** @var \Behat\Behat\Hook\Scope\AfterStepScope $event */
    $type = $event->getStep()->getKeywordType();
    if ($type !== 'Given') {
      if ($this->log) {
        $html_data = $this->getSession()->getDriver()->getContent();
        $unixTime = time();
        $dt = new DateTime("@$unixTime");
        $timestamp =  $dt->format('Y-m-d H-i');
        $file = $timestamp . ' ' . $event->getStep()->getText();
        $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file);
        $file = mb_ereg_replace('/[.]/', '', $file);
        //print_r(getcwd());
        $file_and_path = '/mnt/c/Users/Jonathan/Documents/Drupal/wsl/ahs/features/build/logs/' . $file . '.html';
        file_put_contents($file_and_path, $html_data);
      }
    }
  }


}