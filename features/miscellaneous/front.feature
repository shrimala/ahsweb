@api
Feature: Front page
    In order to have a clean face for users
    As any user
    The front page has special treatment

  Scenario: Picked node is visible on home page for anonymous
    Given I am not logged in
    When I visit "/"
    Then I am visiting "/"
    And I should not see "Access denied"
    # Search is reliably present on discussions home page for logged in users
    And I should not see "Search"
    # Title field is suppressed on front page in node_preprocess
    And I should not see "Home page"