<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\TranslatableContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

/**
 * Uses hooks to monitor Drupal errors and warnings displayed on the page.
 */

class ErrorAndWarningContext extends RawDrupalContext implements TranslatableContext {

  /**
   * Whether the scenario being evaluated has an @hasDrupalError tag
   */
  private $hasDrupalError;

  /**
   * Whether the scenario being evaluated has an @hasDrupalError tag
   */
  private $hasDrupalWarning;

  /**
   * {@inheritDoc}
   */
  public static function getTranslationResources() {
    return glob(__DIR__ . '/../../../../i18n/*.xliff');
  }

  /**
   * @BeforeScenario @hasDrupalError
   */
  public function setHasDrupalError(BeforeScenarioScope $scope)
  {
    $this->hasDrupalError = true;
  }

  /**
   * @AfterScenario @hasDrupalError
   */
  public function unsetHasDrupalError(AfterScenarioScope $scope)
  {
    $this->hasDrupalError = false;
  }

  /**
   * @BeforeScenario @hasDrupalWarning
   */
  public function setHasDrupalWarning(BeforeScenarioScope $scope)
  {
    $this->hasDrupalWarning = true;
  }

  /**
   * @AfterScenario @hasDrupalWarning
   */
  public function unsetHasDrupalWarning(AfterScenarioScope $scope)
  {
    $this->hasDrupalWarning = false;
  }

  /**
   * Warn of unexpected Drupal errors.
   *
   * @AfterStep
   */
  public function checkforDrupalErrorOrWarning($event) {
    /** @var \Behat\Behat\Hook\Scope\AfterStepScope $event */
    $type = $event->getStep()->getKeywordType();
    if ($type !== 'Given') {
      if (!$this->hasDrupalError) {
        $this->assertMessageNotFound('error');
      }
      if (!$this->hasDrupalWarning) {
        $this->assertMessageNotFound('warning');
      }
    }
  }

  /**
   * Checks if a certain message type is present on the page.
   *
   * @param $messageType
   *   string The name of the message type, being the first part of the Drupal selector name.
   *
   * @throws \Exception
   *   If a message of the specified type is found.
   */
  protected function assertMessageNotFound($messageType) {
    $selector = $this->getDrupalSelector($messageType . '_message_selector');
    $selectorObjects = $this->getSession()->getPage()->findAll("css", $selector);
    if (!empty($selectorObjects)) {
      throw new \Exception(sprintf('"%s" on %s', $selectorObjects[0]->getText(), $this->getSession()->getCurrentUrl()));
    }
  }

 

}