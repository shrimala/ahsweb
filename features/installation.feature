@api
Feature: Site Installation
  In order to do anything
    Drupal and key modules needs to be installed 
    
  Scenario: Drupal is installed
    Given I am on "/core/install.php"
    Then I should see "Drupal already installed"

  Scenario: Flysystem is installed
    Given I am logged in as a user with the "administrator" role
    When I visit "admin/config/media/file-system/flysystem"
    Then the response status code should be 200 
