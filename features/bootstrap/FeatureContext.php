<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Element\NodeElement;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * Defines application features from the specific context.
 */

const ELEMENT_MISSING = 0;
const TEXT_MISSING = 1;
const TEXT_INVISIBLE = 2;
const TEXT_VISIBLE = 3;

class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {

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
   * Checks, that an attribute of an element with specified CSS contains specific value.
   *
   * @Then /^the "(?P<attribute>[^"]*)" attribute of "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/
   */
  public function assertElementAttributeContains($element, $attribute, $value)
  {
    $selectorType = 'css';
    $selector = $element;
    $element = $this->elementAttributeExists($selectorType, $selector, $attribute);
    $actual  = $element->getAttribute($attribute);
    $regex   = '/'.preg_quote($value, '/').'/ui';
    if (!preg_match($regex, $actual)) {
      $message = sprintf('The text "%s" was not found in the attribute "%s" of the element matching %s "%s".', $value, $attribute, $selectorType, $selector);
      throw new ExpectationException($message, $this->getSession());
    }
  }
  /**
   * Checks, that an attribute of an element with specified CSS contains specific value.
   *
   * @Then /^the "(?P<attribute>[^"]*)" attribute of "(?P<element>[^"]*)" element should not contain "(?P<value>(?:[^"]|\\")*)"$/
   */
  public function assertElementAttributeNotContains($element, $attribute, $value)
  {
    $selectorType = 'css';
    $selector = $element;
    $element = $this->elementAttributeExists($selectorType, $selector, $attribute);
    $actual  = $element->getAttribute($attribute);
    $regex   = '/'.preg_quote($value, '/').'/ui';
    if (preg_match($regex, $actual)) {
      $message = sprintf('The text "%s" was found in the attribute "%s" of the element matching %s "%s".', $value, $attribute, $selectorType, $selector);
      throw new ExpectationException($message, $this->getSession());
    }
  }
  /**
   * Checks that an attribute exists in an element.
   *
   * @param $selectorType
   * @param $selector
   * @param $attribute
   * @return \Behat\Mink\Element\NodeElement
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function elementAttributeExists($selectorType, $selector, $attribute)
  {
    $element = $this->assertSession()->elementExists($selectorType, $selector);
    if (!$element->hasAttribute($attribute)) {
      $message = sprintf('The attribute "%s" was not found in the element matching %s "%s".', $attribute, $selectorType, $selector);
      throw new ExpectationException($message, $this->getSession());
    }
    return $element;
  }


  /**
   * @Then /^I empty the field "([^"]*)"$/
   */
  public function iEmptyTheField($locator) {
    $session = $this->getSession();
    $page = $session->getPage();
    $field = $page->findField($locator);
    if (null === $field) {
      throw new ElementNotFoundException(
        $this->getSession(), 'form field', 'id|name|label|value', $locator
      );
    }
    $field->setValue("");
  }

  /**
   * @Then I should see :text displayed from the :field field
   */
  public function assertTextDisplayedFromField($text, $field) {
    $result = $this->isTextDisplayedFromField($text, $field);
    switch ($result) {
      case ELEMENT_MISSING:
        throw new \Exception(sprintf("The field '%s' was not found on the page %s", $field, $this->getSession()
          ->getCurrentUrl()));
      case TEXT_MISSING:
        // Field present but text absent
        throw new \Exception(sprintf("The text '%s' was not found in the field '%s' on the page %s", $text, $field, $this->getSession()
          ->getCurrentUrl()));
      case TEXT_INVISIBLE:
        throw new \Exception(sprintf("The text '%s' was hidden in the field '%s' on the page %s", $text, $field, $this->getSession()
          ->getCurrentUrl()));
    }
  }

  /**
   * @Then I should not see :text displayed from the :field field
   */
  public function assertTextNotDisplayedFromField($text, $field) {
    if ($this->isTextDisplayedFromField($text, $field) == TEXT_VISIBLE) {
      throw new \Exception(sprintf("The text '%s' was found in the field '%s' on the page %s", $text, $field, $this->getSession()->getCurrentUrl()));
    }
  }

  /**
   * Is certain text contained with an element containing a certain CSS class.
   *
   * @return int
   *  1=class found and text found in it, 0 = class found, text not in it, -1 = class not found.
   */
  public function isTextDisplayedFromField($text, $field) {
    // Find the field element
    $page = $this->getSession()->getPage();
    $class = ".field--name-" . $this->stringToClassName($field);
    $results = $page->findAll('css', $class);
    if (empty($results)) {
      // The field element could not be found
      return ELEMENT_MISSING;
    }

    // Look through each element that matched .field--name ...
    foreach ($results as $result) {
      if (strpos($result->getText(), $text) === false) {
        // The text is missing from the element.
        return TEXT_MISSING;
      }
      else {
        // The text is present in the element.
        // Is the text actually visible to the site visitor?
        try {
          if ($result->isVisible()) {
            return TEXT_VISIBLE;
          }
          else {
            return TEXT_INVISIBLE;
          }
        }
        catch (Behat\Mink\Exception\UnsupportedDriverActionException $e) {
          // Driver does not support visibility testing, assume visible.
          return TEXT_VISIBLE;
        }
      }
    }
    
    throw new \Exception(sprintf("Unanticipated outcome, looking for the text '%s' in the field '%s' on the page %s", $text, $field, $this->getSession()->getCurrentUrl()));
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
  
}
