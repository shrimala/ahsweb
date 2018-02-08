@api
Feature: User registration
  In order to be able to access the site
  As a new user
  I need to be able to register
@test
  Scenario: User can register
    # A discussion is necessary in order to trigger 'Active discussions' on home page
    Given a discussion content with the title "test"
    Given I am on "/user/register"
    When I fill in the following:
      | First name | UICreated             |
      | Last name  | User                  |
      | E-mail     | UICreated@example.com |
    And I press "Create new account"
    Then I should see "Thankyou for registering"
    And I should see the success message "welcome message with further instructions has been sent to your email address"
    And I should not see the success message "currently pending approval by the site administrator"
  # Test is not working: And I should not see the error message "Access Denied"
    # Implicitly test the site name and email are set correctly
    And new email is sent to "UICreated@example.com":
      | subject                                      | from                |
      | Welcome to the Awakened Heart Sangha website | jonathan@ahs.org.uk |
  # Activate by following email
    When I follow the link to "/user/reset/" from the email to "UICreated"
  # Set password
    And I fill in "pass[pass1]" with "password"
    And I fill in "pass[pass2]" with "password"
    And I press "Log in"
  # Newly confirmed users are logged in and redirected to home page
    Then I should see "Active discussions"
  # New password works
    When I visit "/user/logout"
    And I visit "/user/login"
    When I fill in the following:
      | E-mail   | UICreated@example.com |
      | Password | password              |
    And I press "Log in"
    # Then you're logged in
    Then I should see "Active discussions"
    # Cleanup this user
    #Given I am logged in as an administrator
    #When I visit "/admin/people"
    #And I click "Edit" in the "New name" row
    #And I press "Cancel account"
    #And I select "user_cancel_delete" from "user_cancel_method"
    #And I press "Cancel account"
    # Doesn't work because of the batch job. BatchContext requires JS.

  Scenario: New users must set password
    Given I am on "/user/register"
    When I fill in the following:
      | First name | UICreated2            |
      | Last name  | User                  |
      | E-mail     | UICreated2@example.com |
    And I press "Create new account"
    And I follow the link to "/user/reset/" from the email to "UICreated2"
    Then I should see "Set password"
    When I press "Log in"
    # You're still on the form, it won't let you proceed
    Then I should see "Set password"
