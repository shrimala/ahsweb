@api
Feature: User registration
  In order to be able to access the site
  As a new user
  I need to be able to register

  @email
  Scenario: User can register
    Given I am on "/user/register"
    When I fill in the following:
    | First name | Fred                |
    | Last name  | Bloggs              |
    | E-mail     | fred@bloggs.com |
    And I press "Create new account"
    Then I am visiting "/user/"
