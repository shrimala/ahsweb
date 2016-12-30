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

/**
 * Defines application features from the specific context.
 */


class LocationsContext extends RawMinkContext  implements SnippetAcceptingContext {

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

  //  /**
//   * Checks, that element with specified CSS contains specified text
//   * Example: Then I should see "Batman" in the "heroes_list" element
//   * Example: And I should see "Batman" in the "heroes_list" element
//   *
//   * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
//   */
//  public function assertElementContainsText($element, $text)
//  {
//    $this->assertSession()->elementTextContains('css', $element, $this->fixStepArgument($text));
//  }

// there should (not) be HTML in the...
  
  /**
   * Allow locating of any element using a css expression.
   * 
   * @Transform /(.*)in the "(.*)" element(.*)/
   *
   * @param string $before
   * @param string $locator
   * @param string $after
   * @return string
   */
  public function elementLocation($before ="", $locator, $after ="")
  {
    return $before . 'in the "' . $locator . '" "css" location' . $after;
  }

  /**
   * Allow locating of named Drupal blocks.
   * 
   * @Transform /(.*)in the "(.*)" block(.*)/
   *
   * @param string $before
   * @param string $locator
   * @param string $after
   * @return string
   */
  public function blockLocation($before="", $locator, $after ="")
  {
    return $before . 'in the "block-bartik-' . $this->stringToClassName($locator) . '" "named:id" location' . $after;
  }

  /**
   * Allow locating of headings.
   * 
   * @Transform /(.*)in the heading(.*)/
   *
   * @param string $before
   * @param string $locator
   * @param string $after
   * @return string
   */
  public function headingLocation($before="", $after ="")
  {
    return $before . 'in the "h1,h2,h3,h4,h5,h6" "css" location' . $after;
  }


  /**
   * Transform complete location argument (possibly containg multiple 
   * sub-arguments) into a NodeElement
   * 
   * @Transform /^in the "([^"]*)" "([^"]*)" location$/
   * @Transform /^in the "([^"]*)" "([^"]*)" location in the "([^"]*)" "([^"]*)" location$/
   * @Transform /^in the "([^"]*)" "([^"]*)" location in the "([^"]*)" "([^"]*)" location in the "([^"]*)" "([^"]*)" location$/
   * 
   * @param string $location1
   * @param string $locationType1
   * @param string $location2
   * @param string $locationType2
   * @param string $location3
   * @param string $locationType3
   * @return array

   */
  public function transformLocations($location1, $locationType1, $location2=NULL, $locationType2=NULL, $location3=NULL, $locationType3=NULL)
  {
    $locations = array();
    $locations[] = $this->getSession()->getPage();
    $currentLocationDescription = 'page ' . $this->getSession()->getCurrentUrl();

    if (!empty($location3)) {
      $locations = $this->getLocations($locations, $locationType3, $location3);
      if (count($locations) === 0) {
        throw new \Exception(sprintf('The "%s" %s location was not found in the %s.', $location3, $locationType3, $currentLocationDescription));
      }
      $currentLocationDescription = '"'. $location3 . '" ' . $locationType3 . ' location in the ' . $currentLocationDescription;
    }
    
    if (!empty($location2)) {
      $locations = $this->getLocations($locations, $locationType2, $location2);
      if (count($locations) === 0) {
        throw new \Exception(sprintf('The "%s" %s location was not found in the %s.', $location2, $locationType2, $currentLocationDescription));
      }
      $currentLocationDescription = '"'. $location2 . '" ' . $locationType2 . ' location in the ' . $currentLocationDescription;
    }
    
    $locations = $this->getLocations($locations, $locationType1, $location1);
    if (count($locations) === 0) {
      throw new \Exception(sprintf('The "%s" %s location was not found in the %s.', $location1, $locationType1, $currentLocationDescription));
    }

    return $locations;
  }

  /*
   * Transform a single location argument into an array of NodeElements by
   * searching within each of the current location(s) for elements matching
   * $locator using the $locatorType search method.
   * @param array $locations
   * @param string $locatorType
   * @param string $locator
   */
  protected function getLocations(array $locations, $locatorType, $locator) {

    // $locatorType can be of forms like
    // "named:id" or
    // "namedExact:content"
    // which are not in themselves valid arguments for ->find and
    // need to be translated into
    // ->find("named", array("id", ...)) and
    // ->find("namedExact", array("content", ...))
    if (substr($locatorType, 0, 5) === "named"){
      $locatorTypeParts = explode(":",$locatorType);
      $locatorType = $locatorTypeParts[0];
      $locator = array($locatorTypeParts[1],$locator);
    }

    $newLocations = array();
    foreach ($locations as $location) {
      $theseLocations = $location->findAll($locatorType, $locator);
      $newLocations = array_merge($newLocations, $theseLocations);
    }
    return $newLocations;
  }


  
}
