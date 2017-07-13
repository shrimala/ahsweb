<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\TranslatableContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Mink\Exception\DriverException;

/**
 * Uses hooks to monitor Drupal errors and warnings displayed on the page.
 */

class ErrorAndWarningContext extends RawDrupalContext implements TranslatableContext {

  /**
   * Whether the scenario being evaluated has an @ignoreDrupalErrors tag
   */
  private $ignoreDrupalErrors;

  /**
   * Whether the scenario being evaluated has an @ignoreDrupalWarnings tag
   */
  private $ignoreDrupalWarnings;

  /**
   * {@inheritDoc}
   */
  public static function getTranslationResources() {
    return glob(__DIR__ . '/../../../../i18n/*.xliff');
  }

  /**
   * @BeforeScenario @ignoreDrupalErrors
   */
  public function ignoreDrupalErrors(BeforeScenarioScope $scope)
  {
    $this->ignoreDrupalErrors = true;
  }

  /**
   * @AfterScenario @ignoreDrupalErrors
   */
  public function alertDrupalErrors(AfterScenarioScope $scope)
  {
    $this->ignoreDrupalErrors = false;
  }

  /**
   * @BeforeScenario @ignoreDrupalWarnings
   */
  public function ignoreDrupalWarnings(BeforeScenarioScope $scope)
  {
    $this->ignoreDrupalWarnings = true;
  }

  /**
   * @AfterScenario @ignoreDrupalWarnings
   */
  public function alertDrupalWarnings(AfterScenarioScope $scope)
  {
    $this->ignoreDrupalWarnings = false;
  }

  /**
   * Warn of unexpected Drupal errors.
   *
   * @AfterStep
   */
  public function checkforDrupalErrorOrWarning($event) {
    // Given steps can trigger irrelevant error, e.g. by triggering visits
    // to pages to which the user might not be logged in.
    /** @var \Behat\Behat\Hook\Scope\AfterStepScope $event */
    $type = $event->getStep()->getKeywordType();
    if ($type !== 'Given') {
      if (!$this->ignoreDrupalErrors) {
        $this->assertMessageNotFound('error');
      }
      if (!$this->ignoreDrupalWarnings) {
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
    // If no page has been visited, trying to find something on it fails,
    // and there certainly is no Drupal message.
    try {
      $page = $this->getSession()->getPage();
      $selectorObjects = $page->findAll("css", $selector);
    }
    catch (Exception $e) {
      return;
    }
    // Messages have been found
    if (!empty($selectorObjects)) {
      throw new \Exception(sprintf('"%s" on %s', $selectorObjects[0]->getText(), $this->getSession()->getCurrentUrl()));
    }
  }

 

}