@api
Feature: Site Installation
  In order to do anything
    Drupal and key modules needs to be installed 
    
  Scenario: Drupal is installed
    Given I am on "/core/install.php"
    Then I should see "Drupal already installed"

  Scenario: Flysystem is installed
    Given I am on "/admin/config/media/file-system/flysystem"
    #implicitly checks for http 200 response code
