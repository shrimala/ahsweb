@api
Feature: Site Installation
  Scenario: Installation
    Given I am in the "/core/install.php" path
    Then I should see "To start over, you must empty your existing database"
