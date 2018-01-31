@api
Feature: Password reset
  In order to be able to regain site access if I forget my password
  As a user
  I need to be able to reset my password

  Scenario: User resets password
    # A discussion is necessary in order to trigger 'Active discussions' on home page
    Given a discussion content with the title "Anything"
    Given users:
      | name        | mail            | field_first_name | field_last_name |
      | Fred Bloggs | fred@bloggs.com | Fred             | Bloggs          |
    When I visit "user/login"
    And I fill in the following:
      | E-mail   | bademail@nowhere.com |
      | Password | badpassword          |
    And I press "Log in"
    Then I should see the error message "Unrecognised email or password"
    When I follow "Forgot your password?"
    And I fill in "name" with "fred@bloggs.com"
    And I press "Submit"
    Then I am visiting "/user/login"
    And I should see the success message "Further instructions have been sent to your email address"
    And new email is sent to "fred@bloggs.com":
      | subject        |
      | Reset password |
      # Activate by following email
    When I follow the link to "/user/reset/" from the email to "fred@bloggs.com"
    Then I should see "Set password"
    When I fill in "pass[pass1]" with "password"
    And I fill in "pass[pass2]" with "password"
    And I press "Log in"
    # Newly reset users are logged in
    Then I should see "Active discussions"
      # New password works
    When I visit "/user/logout"
    And I visit "/user/login"
    When I fill in the following:
      | E-mail   | fred@bloggs.com |
      | Password | password              |
    And I press "Log in"
    Then I should see the heading "Active discussions"

  Scenario: User resetting password must choose new password
    Given users:
      | name        | mail            | field_first_name | field_last_name |
      | Fred Bloggs | fred@bloggs.com | Fred             | Bloggs          |
    When I visit "user/login"
    And I fill in the following:
      | E-mail   | bademail@nowhere.com |
      | Password | badpassword          |
    And I press "Log in"
    And I follow "Forgot your password?"
    And I fill in "name" with "fred@bloggs.com"
    And I press "Submit"
    And I follow the link to "/user/reset/" from the email to "fred@bloggs.com"
    Then I should see "Set password"
    When I press "Log in"
    # You're still on the form, it won't let you proceed
    Then I should see "Set password"
