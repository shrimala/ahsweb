<?php

use Drupal\DrupalExtension\Context\MinkContext as DrupalExtensionMinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Exception\ExpectationException;

const PAGE_NOT_FOUND = 'Page not found';

/**
 * Extensions to the Mink Context.
 */
class MinkContext extends DrupalExtensionMinkContext implements SnippetAcceptingContext {
  

  /**
   * Visit a given path, and additionally check for HTTP response code 200.
   *
   * @Then I am visiting :path
   *
   * @throws ExpectationException
   */
  public function assertAmVisitingPath($desiredPath) {
    $actualPath = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);
    if ($actualPath != $desiredPath) {
      $message = sprintf('The actual path "%s" is not the desired path "%s"', $actualPath, $desiredPath);
      throw new ExpectationException($message, $this->getSession());
    }
    // If available, add extra validation that this is a 200 response.
    try {
      $this->getSession()->getStatusCode();
      $this->assertHttpResponse('200');
    }
    catch (UnsupportedDriverActionException $e) {
      // Simply continue on, as this driver doesn't support HTTP response codes.
    }
    if ($this->pageNotFound()) {
      $message = sprintf('The page title contains "%s"', PAGE_NOT_FOUND);
      throw new ExpectationException($message, $this->getSession());
    }
  }

  /**
   * Detects Page not found errors by inspecting page titles,
   * a workaround for lack of response code inspection in Selenium.
   **
   * return bool
   */
  protected function pageNotFound() {
    $titleElement = $this->getSession()->getPage()->find('css', 'head title');
    if ($titleElement === null) {
      throw new Exception('Page title element was not found!');
    }
    else {
      $title = $titleElement->getText();
      if (strpos($title, PAGE_NOT_FOUND) !== FALSE) {
        return TRUE;
      }
      return FALSE;
    }
  }

  /**
   * @Given the :module module is installed
   */
  public function assertModuleIsInstalled($module) {
    $this->visitPath('/admin/modules');
    $checkbox = $this->assertSession()->fieldExists($module);
    if (!$checkbox->isChecked()) {
      throw new \Exception(sprintf("The module '%s' does not appear to be enabled", $module));
    }
  }

  /**
   * @When I comment :comment
   */
  public function iComment($comment) {
    $this->fillField('comment[value]', $comment);
    $this->pressButton('Save');
  }


}

