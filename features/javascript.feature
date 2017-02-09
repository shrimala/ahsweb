@api @skip
Feature: Javascript testing is active
  In order to test advanced UI functions
  As a test developer
  I need to be sure javascript testing is possible

  @javascript @skip
  Scenario:
    When I visit "/user/login"
    And I fill in the following:
      | E-mail   | fredbloggs |
      | Password | test            |
    And I press "Log in"
    Then I should see "Please include an '@' in the email"

  @javascript @skip
  Scenario:
    Given a "discussion" with the title "Test"
    And I am logged in as an "authenticated user"
    When I visit "/discuss/test"
    Then I should get a 200 HTTP response
    And the "Title" field should contain "Test"

  @javascript
  Scenario:
    Given I am logged in as an "authenticated user"
