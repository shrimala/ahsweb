<?php

use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Context\Context;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class SauceContext implements SnippetAcceptingContext
{

  /** @BeforeScenario */
  public function before($scope)
  {
    $username = getenv("SAUCE_USERNAME");
    $access_key = getenv("SAUCE_ACCESS_KEY");
    $platform = getenv("platform");
    $browser_name = getenv("browserName");
    $version = getenv("version");
    $scenario_name = $scope->getScenario()->gettitle();
    $feature_name = $scope->getFeature()->gettitle();
    $host = sprintf(
      'https://%s:%s@ondemand.saucelabs.com:443/wd/hub',
      $username,
      $access_key
    );
    
    if (empty($platform)) {
      $platform ="Windows 10";
    }
    if (empty($browser_name)) {
      $browser_name ="firefox";
    }
    if (empty($version)) {
      $version ="latest";
    }
    
    $desired_capabilities = new stdClass;
    $desired_capabilities->platform = $platform;
    $desired_capabilities->browserName = $browser_name;
    $desired_capabilities->version = $version;
    $desired_capabilities->name = $feature_name." - ". $scenario_name;
    $this->driver = RemoteWebDriver::create($host, $desired_capabilities);
  }
  /**
   * @Given I am on the guinea pig site
   */
  public function iAmOnTheGuineaPigSite()
  {
    $this->driver->get('https://saucelabs.com/test/guinea-pig');
  }
  /**
   * @When I click on the link
   */
  public function iClickOnTheLink()
  {
    $link = $this->driver->findElement(WebDriverBy::id('i am a link'));
    $link->click();
  }
  /**
   * @Then I should be on a new page
   */
  public function iShouldBeOnANewPage()
  {
    $title = $this->driver->getTitle();
    PHPUnit_Framework_Assert::assertEquals($title, "I am another page title - Sauce Labs");
  }
  /** @AfterScenario */
  public function after($scope)
  {
    $jobId = $this->driver->getSessionId();
    $result = "false";
    $test_results = $scope->getTestResult();
    $passed = $test_results->getResultCode();
    $scenario_name = $scope->getScenario()->gettitle();
    $feature_name = $scope->getFeature()->gettitle();
    if ($passed === 0) {
      $result = "true";
    }
    $this->modifySauceJob(
      sprintf(
        '{"passed": %s}',
        $result
      ),
      $jobId
    );
    $this->driver->quit();
    print "SauceOnDemandSessionID=".$jobId." job-name=".$feature_name." - ".$scenario_name;
  }
  public function getSessionId($event)
  {
    $scenario = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
    $context = $event->getContext();
    $url = $context->getSession()->getDriver()->getWebDriverSession()->getUrl();
    $parts = explode('/', $url);
    $sessionId = array_pop($parts);
    return $sessionId;
  }
  public function modifySauceJob($payload, $session_id) {
    $username = getenv("SAUCE_USERNAME");
    $access_key = getenv("SAUCE_ACCESS_KEY");
    $ch = curl_init(
      sprintf(
        'https://%s:%s@saucelabs.com/rest/v1/%s/jobs/%s',
        $username,
        $access_key,
        $username,
        $session_id
      )
    );
    $length = strlen($payload);
    $fh = fopen('php://temp', 'rw+');
    fwrite($fh, $payload);
    rewind($fh);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_INFILE, $fh);
    curl_setopt($ch, CURLOPT_INFILESIZE, $length);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
  }
}