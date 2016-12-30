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

/**
 * Defines application features from the specific context.
 */

class LocatableContext extends RawLocatableContext implements SnippetAcceptingContext {

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
   * @xxxThen /^xxxI should ([^ ]*\s?)see "([^"]*)" (in the .*)$/
   *
   * @param string $text
   * @param array $locations
   */
  public function assertTextInLocation($qualifier = '', $text, array $locations = [])
  {
    $behavior = function ($location) use ($text) {
      return $location->findAll('named', array('content', $text));
    };
    $exceptionMessage = sprintf("Text '%s' not found", $text);
    $this->assertInLocations($behavior, $locations, $exceptionMessage, $this->interpretQualifier($qualifier));
  }

  /**
   * @xxxThen /^xxxI should ([^ ]*\s?)grok "([^"]*)" (in the .*)$/
   *
   * @param string $text
   * @param array $locations
   */
  public function assertGrokInLocation($qualifier = '', $text, array $locations = [])
  {
    $behavior = function ($location) use ($text) {
      return $this->assertSession()->elementExists('css', $text, $location);
      //return $location->findAll('named', array('content', $text));
    };
    $exceptionMessage = sprintf("Element '%s' not found", $text);
    $this->assertInLocations($behavior, $locations, $exceptionMessage, $this->interpretQualifier($qualifier));
  }



  /**
   * Presses button with specified id|name|title|alt|value in a location
   * Example: When I press "Log In" in the "Account" block
   * Example: And I press "Log In" in the "Header" region
   *
   * @When /^(?:|I )press "(?P<button>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function pressButton($button, array $locations = [])
  {
    $button = $this->fixStepArgument($button);
    $behavior = function ($location) use ($button) {
      return $location->pressButton($button);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Clicks link with specified id|title|alt|text in a location
   * Example: When I follow "Log In" in the "Account" block
   * Example: And I follow "Log In"
   *
   * @When /^(?:|I )follow "(?P<link>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function clickLink($link, array $locations = [])
  {
    $link = $this->fixStepArgument($link);
    $behavior = function ($location) use ($link) {
      return $location->clickLink($link);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Fills in form field with specified id|name|label|value in a location
   * Example: When I fill in "username" with: "bwayne" in the "Account" block
   * Example: And I fill in "bwayne" for "username"  in the "Header" region
   *
   * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)" (in the .*)$/
   * @xxx /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with: (in the .*)$/
   * @When /^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function fillField($field, $value, array $locations = [])
  {
    $field = $this->fixStepArgument($field);
    $value = $this->fixStepArgument($value);
    $behavior = function ($location) use ($field, $value) {
      return $location->fillField($field, $value);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Fills in form fields with provided table in a location
   * Example: When I fill in the following in the "Account" block:
   *              | username | bruceWayne |
   *              | password | iLoveBats123 |
   * Example: And I fill in the following  in the "Header" region:
   *              | username | bruceWayne |
   *              | password | iLoveBats123 |
   *
   * @When /^(?:|I )fill in the following (in the .*):$/
   */
  public function fillFields(array $locations = [], TableNode $fields)
  {
    foreach ($fields->getRowsHash() as $field => $value) {
      $this->fillField($field, $value, $locations);
    }
  }

  /**
   * Selects option in select field with specified id|name|label|value in a location
   * Example: When I select "Bats" from "user_fears" in the "Account" block
   * Example: And I select "Bats" from "user_fears"  in the "Header" region
   *
   * @When /^(?:|I )select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function selectOption($select, $option, array $locations = [])
  {
    $select = $this->fixStepArgument($select);
    $option = $this->fixStepArgument($option);
    $behavior = function ($location) use ($select, $option) {
      return $location->selectFieldOption($select, $option);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Selects additional option in select field with specified id|name|label|value in a location
   * Example: When I additionally select "Deceased" from "parents_alive_status" in the "Account" block
   * Example: And I additionally select "Deceased" from "parents_alive_status" in the "Header" region
   *
   * @When /^(?:|I )additionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function additionallySelectOption($select, $option, array $locations = [])
  {
    $select = $this->fixStepArgument($select);
    $option = $this->fixStepArgument($option);
    $behavior = function ($location) use ($select, $option) {
      return $location->selectFieldOption($select, $option, true);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks checkbox with specified id|name|label|value in a location
   * Example: When I check "Pearl Necklace" from "itemsClaimed" in the "Account" block
   * Example: And I check "Pearl Necklace" from "itemsClaimed" in the "Header" region
   *
   * @When /^(?:|I )check "(?P<option>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function checkOption($option, array $locations = [])
  {
    $option = $this->fixStepArgument($option);
    $behavior = function ($location) use ($option) {
      return $location->checkField($option);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Unchecks checkbox with specified id|name|label|value in a location
   * Example: When I uncheck "Broadway Plays" from "hobbies" in the "Account" block
   * Example: And I uncheck "Broadway Plays" from "hobbies" in the "Header" region
   *
   * @When /^(?:|I )uncheck "(?P<option>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function uncheckOption($option, array $locations = [])
  {
    $option = $this->fixStepArgument($option);
    $behavior = function ($location) use ($option) {
      return $location->uncheckField($option);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Attaches file to field with specified id|name|label|value in a location
   * Example: When I attach "bwayne_profile.png" to "profileImageUpload" in the "Account" block
   * Example: And I attach "bwayne_profile.png" to "profileImageUpload" in the "Header" region
   *
   * @When /^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function attachFileToField($field, $path, array $locations = [])
  {
    $field = $this->fixStepArgument($field);

    if ($this->getMinkParameter('files_path')) {
      $fullPath = rtrim(realpath($this->getMinkParameter('files_path')), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;
      if (is_file($fullPath)) {
        $path = $fullPath;
      }
    }
    
    $behavior = function ($location) use ($field, $path) {
      return $location->attachFileToField($field, $path);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that page contains specified text in a location
   * Example: Then I should see "Who is the Batman?" in the "Account" block
   * Example: And I should see "Who is the Batman?" in the "Header" region
   *
   * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function assertLocationContainsText($text, $locations)
  {
    $text = $this->fixStepArgument($text);
    $behavior = function ($location) use ($text) {
      return $this->locationTextContains($text, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that page doesn't contain specified text in a location
   * Example: Then I should not see "Batman is Bruce Wayne" in the "Account" block
   * Example: And I should not see "Batman is Bruce Wayne" in the "Header" region
   *
   * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function assertLocationNotContainsText($text, $locations)
  {
    $text = $this->fixStepArgument($text);
    $behavior = function ($location) use ($text) {
      return $this->locationTextNotContains($text, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that page contains text matching specified pattern in a location
   * Example: Then I should see text matching "Batman, the vigilante" in the "Account" block
   * Example: And I should not see "Batman, the vigilante" in the "Header" region
   *
   * @Then /^(?:|I )should see text matching (?P<pattern>"(?:[^"]|\\")*") (in the .*)$/
   */
  public function assertLocationMatchesText($pattern, $locations)
  {
    $pattern = $this->fixStepArgument($pattern);
    $behavior = function ($location) use ($pattern) {
      return $this->locationTextMatches($pattern, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that page doesn't contain text matching specified pattern in a location
   * Example: Then I should see text matching "Bruce Wayne, the vigilante" in the "Account" block
   * Example: And I should not see "Bruce Wayne, the vigilante" in the "Header" region
   *
   * @Then /^(?:|I )should not see text matching (?P<pattern>"(?:[^"]|\\")*") (in the .*)$/
   */
  public function assertLocationNotMatchesText($pattern, $locations)
  {
    $pattern = $this->fixStepArgument($pattern);
    $behavior = function ($location) use ($pattern) {
      return $this->locationTextNotMatches($pattern, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that HTML response contains specified string in a location
   * Example: Then the response should contain "Batman is the hero Gotham deserves." in the "Account" block
   * Example: And the response should contain "Batman is the hero Gotham deserves." in the "Header" region
   *
   * @Then /^the response should contain "(?P<text>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function assertLocationResponseContains($text, $locations)
  {
    $text = $this->fixStepArgument($text);
    $behavior = function ($location) use ($text) {
      return $this->locationResponseContains($text, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that HTML response doesn't contain specified string in a location
   * Example: Then the response should not contain "Bruce Wayne is a billionaire, play-boy, vigilante." in the "Account" block
   * Example: And the response should not contain "Bruce Wayne is a billionaire, play-boy, vigilante." in the "Header" region
   *
   * @Then /^the response should not contain "(?P<text>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function assertLocationResponseNotContains($text, $locations)
  {
    $text = $this->fixStepArgument($text);
    $behavior = function ($location) use ($text) {
      return $this->locationResponseNotContains($text, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that element with specified CSS exists on page in a location
   * Example: Then I should see a "body" element in the "Account" block
   * Example: And I should see a "body" element in the "Header" region
   *
   * @Then /^(?:|I )should see an? "(?P<element>[^"]*)" element (in the .*)$/
   */
  public function assertElementOnPage($element, array $locations = [])
  {
    $behavior = function ($location) use ($element) {
      return $this->assertSession()->elementExists('css', $element, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that element with specified CSS doesn't exist on page in a location
   * Example: Then I should not see a "canvas" element in the "Account" block
   * Example: And I should not see a "canvas" element in the "Header" region
   *
   * @Then /^(?:|I )should not see an? "(?P<element>[^"]*)" element (in the .*)$/
   */
  public function assertElementNotOnPage($element, array $locations = [])
  {
    $behavior = function ($location) use ($element) {
      return $this->assertSession()->elementNotExists('css', $element, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

    /**
   * Checks, that a form field in a location has specified value
   * Example: Then the value should be "bwayne" in the "username" form field
   * Example: And the value should be "bwayne" in the "username" form field
   *
   * @Then /^the value should be "(?P<value>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function assertFieldContains($value, array $locations = [])
  {
    $value = $this->fixStepArgument($value);
    $behavior = function ($location) use ($value) {
      return $this->fieldValueEquals($value, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that form field in a location doesn't have specified value
   * Example: Then the value should not be "batman" in the "username" form field
   * Example: And the value should not be "batman" in the "username" form field
   *
   * @Then /^the value should not be "(?P<field>(?:[^"]|\\")*)" (in the .*)$/
   */
  public function assertFieldNotContains($value, array $locations = [])
  {
    $value = $this->fixStepArgument($value);
    $behavior = function ($location) use ($value) {
      return $this->fieldValueNotEquals($value, $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that (?P<num>\d+) CSS elements exist on the page in a location
   * Example: Then I should see 5 "div" elements in the "Account" block
   * Example: And I should see 5 "div" elements in the "Header" region
   *
   * @Then /^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements? (in the .*)$/
   */
  public function assertNumElements($num, $element, array $locations = [])
  {
    $behavior = function ($location) use ($element, $num) {
      return $this->assertSession()->elementsCount('css', $element, intval($num), $location);
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that checkbox with specified in|name|label|value is checked in a location
   * Example: Then the "remember_me" checkbox should be checked in the "Account" block
   * Example: And the "remember_me" checkbox is checked in the "Header" region
   *
   * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked (in the .*)$/
   * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" (?:is|should be) checked (in the .*)$/
   */
  public function assertCheckboxChecked($checkbox, array $locations = [])
  {
    $behavior = function ($location) use ($checkbox) {
      return $this->assertSession()->checkboxChecked($this->fixStepArgument($checkbox));
    };
    $this->behaveInLocations($behavior, $locations);
  }

  /**
   * Checks, that checkbox with specified in|name|label|value is unchecked in a location
   * Example: Then the "newsletter" checkbox should be unchecked in the "Account" block
   * Example: Then the "newsletter" checkbox should not be checked in the "Account" block
   * Example: And the "newsletter" checkbox is unchecked in the "Header" region
   *
   * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should not be checked (in the .*)$/
   * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" should (?:be unchecked|not be checked) (in the .*)$/
   * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" is (?:unchecked|not checked) (in the .*)$/
   */
  public function assertCheckboxNotChecked($checkbox, array $locations = [])
  {
    $behavior = function ($location) use ($checkbox) {
      return $this->assertSession()->checkboxNotChecked($this->fixStepArgument($checkbox));
    };
    $this->behaveInLocations($behavior, $locations);
  }

}
