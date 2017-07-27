@api
Feature: User registration
  In order to be able to access the site
  As a new user
  I need to be able to register

  Scenario: User can register
    Given I am on "/user/register"
    When I fill in the following:
      | First name | UICreated             |
      | Last name  | User                  |
      | E-mail     | UICreated@example.com |
    And I press "Create new account"
    Then I am visiting "/user/login"
    And I should see the success message "welcome message with further instructions has been sent to your email address"
    And I should not see the success message "currently pending approval by the site administrator"
  # Test is not working: And I should not see the error message "Access Denied"
    And new email is sent to "UICreated@example.com":
      | subject |
      | Welcome |
  # Activate by following email
    When I follow the link to "/user/reset/" from the email to "UICreated"
    Then I should see "This is a one-time login for UICreated User"
    When I press "Log in"
  # Contact settings hidden on form
    Then I should not see "Contact settings"
    When I fill in "name" with "New name"
  # Set password
    And I fill in "pass[pass1]" with "password"
    And I fill in "pass[pass2]" with "password"
    And I press "Save"
    Then I should see "New name"
  # Newly confirmed users are logged in
    When I visit "/"
    Then I should see "Active discussions"
  # New password works
    When I visit "/user/logout"
    And I visit "/user/login"
    When I fill in the following:
      | E-mail   | UICreated@example.com |
      | Password | password              |
    And I press "Log in"
    Then I should see the heading "New name"
    # Cleanup this user
    #Given I am logged in as an administrator
    #When I visit "/admin/people"
    #And I click "Edit" in the "New name" row
    #And I press "Cancel account"
    #And I select "user_cancel_delete" from "user_cancel_method"
    #And I press "Cancel account"
    # Doesn't work because of the batch job. BatchContext requires JS.