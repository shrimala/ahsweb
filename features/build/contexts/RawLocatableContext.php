<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Element\NodeElement;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\RawMinkContext;
use Drupal\Tests\Core\TestUrl;

const ALL = 2;
const ANY = 1;
const SOME = 1;
const NONE = 0;

/**
 * Defines application features from the specific context.
 */

class RawLocatableContext extends RawMinkContext implements SnippetAcceptingContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }
  
  /**
   * Make a string safe to be a CSS class name.
   *
   * @return string
   *  String with only letters, numbers and dashes. Underscores converted to dashes.
   */
  protected function stringToClassName($string){
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
  }

  /**
   * Assert a behavior in multiple locations.
   * Can assert
   *
   * @return string
   *  String with only letters, numbers and dashes. Underscores converted to dashes.
   */
protected function assertInLocations($behavior, array $locations = [], $exceptionMessage, $expected = ANY, $locationMatch = 'ALL')
  {
    // Get the response for the behavior in each location.
    $results =  $this->behaveInLocations($behavior, $locations, $locationMatch);

    // Evaluate the number of locations which returned
    // a non-empty result for the behavior.
    $count = 0;
    foreach ($results as $result) {
      if (!empty($result) && count($result) > 0) {
        $count ++;
      }
    }
    if ($count === 0) {
      $actual = NONE;
    }
    elseif ($count < count($results)) {
      $actual = SOME;
    }
    elseif ($count === count($results)) {
      $actual = ALL;
    }

    // Raise an exception if we didn't get the results we expected
    if (!(($actual === ALL && $expected === ANY) || ($actual === $expected))) {
      if (count($results) === 1) {
        $exceptionMessage .= ' in the location.';  
      }
      elseif ($actual === SOME) {
          $exceptionMessage .= ' in some of the location(s).';
      }
      elseif ($actual === NONE  && $expected === ALL) {
          $exceptionMessage .= ' in any of the locations';
      }
      elseif ($actual == ALL && $expected === NONE) {
          $exceptionMessage .= ' in all of the locations';
      }
      throw new \Exception($exceptionMessage);
    }
    
  }

  protected function behaveInLocations($behavior, array $locations = [], $locationMatch = 'ALL')
  {
    if (count($locations) == 0) {
      $locations[] = $this->getSession()->getPage();
    }
    $results = array();
    foreach ($locations as $location) {
      $results[] = $behavior($location);
      if ($locationMatch === 'FIRST') {
        break;
      }
    }
    return $results;
  }

  protected function interpretQualifier($qualifier, $default = ANY) {
    $qualifier = trim($qualifier);
    switch ($qualifier) {
      case 'always':
        return ALL;
      case 'never':
        return NONE;
      case 'not':
        return NONE;
      case 'sometimes':
        return ANY;
      default:
        return $default;
    }
  }

  /**
   * Returns fixed step argument (with \\" replaced back to ")
   *
   * @param string $argument
   *
   * @return string
   */
  protected function fixStepArgument($argument)
  {
    return str_replace('\\"', '"', $argument);
  }

  /**
   * Checks that the text contains specified text.
   *
   * @param string $text
   *
   * @throws ResponseTextException
   */
  public function locationTextContains($text, NodeElement $location)
  {
    $actual = $location->getText();
    $actual = preg_replace('/\s+/u', ' ', $actual);
    $regex = '/'.preg_quote($text, '/').'/ui';
    $message = sprintf('The text "%s" was not found anywhere in the location.', $text);

    $this->assertResponseText((bool) preg_match($regex, $actual), $message);
  }

  /**
   * Checks that the text does not contains specified text.
   *
   * @param string $text
   *
   * @throws ResponseTextException
   */
  public function locationTextNotContains($text, NodeElement $location)
  {
    $actual = $location->getText();
    $actual = preg_replace('/\s+/u', ' ', $actual);
    $regex = '/'.preg_quote($text, '/').'/ui';
    $message = sprintf('The text "%s" appears in the location, but it should not.', $text);

    $this->assertResponseText(!preg_match($regex, $actual), $message);
  }

  /**
   * Checks that the text matches regex.
   *
   * @param string $regex
   *
   * @throws ResponseTextException
   */
  public function locationTextMatches($regex, NodeElement $location)
  {
    $actual = $location->getText();
    $message = sprintf('The pattern %s was not found anywhere in the text of the location.', $regex);

    $this->assertResponseText((bool) preg_match($regex, $actual), $message);
  }

  /**
   * Checks that the text does not matches regex.
   *
   * @param string $regex
   *
   * @throws ResponseTextException
   */
  public function locationTextNotMatches($regex, NodeElement $location)
  {
    $actual = $location->getText();
    $message = sprintf('The pattern %s was found in the text of the location, but it should not.', $regex);

    $this->assertResponseText(!preg_match($regex, $actual), $message);
  }

  /**
   * Checks that the HTML (response content) contains text.
   *
   * @param string $text
   *
   * @throws ExpectationException
   */
  public function locationResponseContains($text, NodeElement $location)
  {
    $actual = $location->getContent();
    $regex = '/'.preg_quote($text, '/').'/ui';
    $message = sprintf('The string "%s" was not found in the HTML of the location.', $text);

    $this->assert((bool) preg_match($regex, $actual), $message);
  }

  /**
   * Checks that the HTML (response content) does not contains text.
   *
   * @param string $text
   *
   * @throws ExpectationException
   */
  public function locationResponseNotContains($text, NodeElement $location)
  {
    $actual = $location->getContent();
    $regex = '/'.preg_quote($text, '/').'/ui';
    $message = sprintf('The string "%s" appears in the HTML of the location, but it should not.', $text);

    $this->assert(!preg_match($regex, $actual), $message);
  }

  /**
   * Checks that the HTML (response content) matches regex.
   *
   * @param string $regex
   *
   * @throws ExpectationException
   */
  public function locationResponseMatches($regex, NodeElement $location)
  {
    $actual = $location->getContent();
    $message = sprintf('The pattern %s was not found in the HTML response of the location.', $regex);

    $this->assert((bool) preg_match($regex, $actual), $message);
  }

  /**
   * Checks that the HTML (response content) does not matches regex.
   *
   * @param $regex
   *
   * @throws ExpectationException
   */
  public function locationResponseNotMatches($regex, NodeElement $location)
  {
    $actual = $location->getContent();
    $message = sprintf('The pattern %s was found in the HTML of the location, but it should not.', $regex);

    $this->assert(!preg_match($regex, $actual), $message);
  }

  /**
   * Checks that specific field have provided value.
   *
   * @param string             $field     field id|name|label|value
   * @param string             $value     field value
   * @param TraversableElement $container document to check against
   *
   * @throws ExpectationException
   */
  public function fieldValueEquals($value, NodeElement $node)
  {
    $actual = $node->getValue();
    $regex = '/^'.preg_quote($value, '/').'$/ui';

    $message = sprintf('The form field value is "%s", but "%s" expected.', $actual, $value);

    $this->assert((bool) preg_match($regex, $actual), $message);
  }

  /**
   * Checks that specific field have provided value.
   *
   * @param string             $field     field id|name|label|value
   * @param string             $value     field value
   * @param TraversableElement $container document to check against
   *
   * @throws ExpectationException
   */
  public function fieldValueNotEquals($value, NodeElement $node)
  {
    $actual = $node->getValue();
    $regex = '/^'.preg_quote($value, '/').'$/ui';

    $message = sprintf('The form field value is "%s", but it should not be.', $actual);

    $this->assert(!preg_match($regex, $actual), $message);
  }

  /**
   * Asserts a condition.
   *
   * @param bool   $condition
   * @param string $message   Failure message
   *
   * @throws ExpectationException when the condition is not fulfilled
   */
  private function assert($condition, $message)
  {
    if ($condition) {
      return;
    }

    throw new ExpectationException($message, $this->session->getDriver());
  }

  /**
   * Asserts a condition involving the response text.
   *
   * @param bool   $condition
   * @param string $message   Failure message
   *
   * @throws ResponseTextException when the condition is not fulfilled
   */
  private function assertResponseText($condition, $message)
  {
    if ($condition) {
      return;
    }

    throw new ResponseTextException($message, $this->session->getDriver());
  }
  
}
