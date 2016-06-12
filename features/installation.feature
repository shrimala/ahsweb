@api
Feature: Site Installation
  In order to do anything
    Drupal needs to be installed 
    
  Scenario: Installation
    Given I am on "/core/install.php"
    Then I should see "Drupal already installed"
