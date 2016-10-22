<?php

use Drupal\DrupalExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Exception\ExpectationException;

/**
 * Extensions to the Mink Context.
 */
class ExtendedMinkContext extends MinkContext implements SnippetAcceptingContext {
  

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
  }

}
