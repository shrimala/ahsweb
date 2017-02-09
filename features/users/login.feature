@api
Feature: User login
  In order to be able to access the site
  As an existing user
  I need to be able to login

  Scenario: User can login using email
    Given users:
      | name        | mail            | pass |
      | Fred Bloggs | fred@bloggs.com | test |
    When I visit "/user/login"
    When I fill in the following:
    | E-mail   | fred@bloggs.com |
    | Password | test            |
    And I press "Log in"
    Then I should see the heading "Fred Bloggs"

  @hasDrupalError
  Scenario: Redirect on login
    Given users:
      | name        | mail            | pass |
      | Fred Bloggs | fred@bloggs.com | test |
    When I visit "/discuss/add"
    Then I should see the error message "Access denied"
    And I should see "Login"
    When I fill in the following:
      | E-mail   | fred@bloggs.com |
      | Password | test            |
    And I press "Log in"
    Then I am visiting "/discuss/add"
    And I should not see the heading "Fred Bloggs"