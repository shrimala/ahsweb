<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Element\NodeElement;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException;
use Drupal\DrupalExtension\Hook\Scope\EntityScope;

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

  /**
   * Checks if a set of elements matches the values in a table.
   *
   * @Then I should see :elements_css( elements):
   *
   * @param $elements_css
   *   string The id|name|title|alt|value (css selector) of the element that constitutes the list item
   *
   * @throws \Exception
   *   If elements do not match table.
   */
  public function assertElements(TableNode $table, $elements_css) {
    $page =$this->getSession()->getPage();
    $list = $this->findByRegionOrCss($page,'findAll', $elements_css);
    $expectedItems = $table->getHash();

    // Prepare text output for error messages
    $listText = "\n";
    foreach ($list as $listItem) {
      $listText .= $listItem->getText() . "\n";
    }

    if (empty($list)) {
      throw new \Exception(sprintf("No '%s' were found on the page %s", $elements_css, $this->getSession()
        ->getCurrentUrl()));
    }
    if (count($list) !== count($expectedItems)) {
      throw new \Exception(sprintf("%s '%s' were found on the page %s, but %s list items were expected. The found items were:", count($list), $elements_css, $this->getSession()
        ->getCurrentUrl(), count($expectedItems), $listText));
    }

    foreach ($expectedItems as $n => $expectedItem) {
      $rowElement = $list[$n];
      foreach ($expectedItem as $expectedItemType => $expectedItemValue) {

        // If the value in the column for this row is blank, skip it.
        if (empty($expectedItemValue)) {
          break;
        }

        $rowMessage = sprintf('#%s of the "%s" on the page %s', $n + 1, $elements_css, $this->getSession()
          ->getCurrentUrl());
        $sharedMessage = $rowMessage;

        // A test for text anywhere in row element is requested.
        if ($expectedItemType === 'contains'
          || $expectedItemType === ':contains'
          || $expectedItemType === ': contains') {
          $find = FALSE;
          $contains = TRUE;
          $element = $rowElement;
        }
        // A test using a more specific sub-element is requested.
        else {
          // Prepare to test presence of a sub-element
          $find = TRUE;
          $contains = FALSE;
          $selector = $expectedItemType;

          // Prepare to test for text in a sub-element
          if (substr($expectedItemType, -9, 9) === ':contains') {
            $contains = TRUE;
            $selector = substr($expectedItemType, 0, strlen($expectedItemType) - 9);
          }
          if (substr($expectedItemType, -10, 10) === ': contains') {
            $contains = TRUE;
            $selector = substr($expectedItemType, 0, strlen($expectedItemType) - 10);
          }
          if (strtolower($expectedItemValue) !== 'yes' && strtolower($expectedItemValue) !== 'no') {
            $contains = TRUE;
          }
        }

        // Set up test expectations
        $expected = TRUE;
        if (strtolower($expectedItemValue) === 'no') {
          $expected = FALSE;
        }
        $negationText = $expected ? "not " : "";

        // Find the sub-element
        if ($find) {
          $element = $this->findByRegionOrCss($rowElement, 'find', $selector);
          $actual = !($element === null);

          $message = sprintf('"%s" was %sfound in %s', $selector, $negationText, $sharedMessage);
          $sharedMessage = sprintf('in the "%s" in %s', $selector, $sharedMessage);
        }

        // Test element contains text
        if ($contains && $element) {
          $regex = '/' . preg_quote($expectedItemValue, '/') . '/ui';
          $actual =  (((bool) preg_match($regex, $element->getText())));
          $message = sprintf('The text "%s" was not found in %s. The actual text was "%s". The full list was: %s', $expectedItemValue, $sharedMessage, $element->getText(), $listText);
        }

        // Classes can additionally match on element as within element
        if (substr($expectedItemType, 0, 1) === '.'
          && strpos($expectedItemType, ":") === FALSE
          && $actual === FALSE) {
          $class = substr($expectedItemType, 1);
          $actual = $rowElement->hasClass($class);
          $message = sprintf('The "%s" class was %sfound on or in %s', $class, $negationText, $rowMessage);
        }

        if ($expected !== $actual) {
          throw new \Exception($message);
        }


      }
    }
  }

  protected function findByRegionOrCss($element, $method, $selector) {
    try {
      $found = $element->$method('region', $selector);
    }
    catch (Exception $e) {
      $found = $element->$method('css', $selector);
    }
    return $found;
  }

  
  
}