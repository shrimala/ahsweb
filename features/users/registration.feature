@api @skip
Feature: User registration
  In order to be able to access the site
  As a new user
  I need to be able to register

  @email @hasDrupalError @hasDrupalWarning
  Scenario: User can register
    Given I am on "/user/register"
    When I fill in the following:
    | First name | UICreated             |
    | Last name  | User                  |
    | E-mail     | UICreated@example.com |
    And I press "Create new account"
    Then I am visiting "/user/login"
    And I should see the success message "A welcome message with further instructions has been sent to your email address"
    #Ideally should have cleanup for this user