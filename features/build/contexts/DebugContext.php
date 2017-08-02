<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\TranslatableContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;

/**
 * Uses hooks to output Drupal messages (including devel messages) for debugging
 */

class DebugContext extends RawDrupalContext implements TranslatableContext {

  /**
   * Debug output for this scenario
   */
  private $output = array();


  /**
   * Whether the scenario being evaluated has an @debug tag
   */
  private $debug;

  /**
   * {@inheritDoc}
   */
  public static function getTranslationResources() {
    return glob(__DIR__ . '/../../../../i18n/*.xliff');
  }

  /**
   * @BeforeScenario @debug
   */
  public function setDebug(BeforeScenarioScope $scope) {
    $this->debug = true;
  }

  /**
   * @AfterScenario @debug
   */
  public function unsetDebug(AfterScenarioScope $scope) {
    $this->debug = false;
    $this->output = [];
  }

  /**
   * Prepare debugging output for this step
   *
   * @AfterStep
   */
  public function prepareDebugOutput(AfterStepScope $scope) {
    //if ($this->debug) {
    $this->output[] = $scope->getStep()->getText();
    try {
      $this->output[] = $this->getSession()->getCurrentUrl();
      $messageTypes = ['error', 'warning', 'success'];
      foreach ($messageTypes as $messageType) {
        $selector = $this->getDrupalSelector($messageType . '_message_selector');
        $messagesOfType = $this->getSession()
          ->getPage()
          ->findAll("css", $selector);
        foreach ($messagesOfType as $message) {
          $this->output[] = $message->getText();
        }
      }
    }
    catch(Exception $e) {}


    if (99 === $scope->getTestResult()->getResultCode()) {
      throw new \Exception(sprintf("Extra debugging information is available: \n %s", join("\n", $this->output)));
    }

  }

  /**
   * @Then I dump the page
   */
  public function testMailCollectorIsNotEnabled()
  {
    var_dump($this->getSession()->getPage()->getText());
  }

}