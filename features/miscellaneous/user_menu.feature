@api
Feature: User menu
  In order to be able to access personal settings
  As an existing user
  I have a user menu

  Scenario: User menu uses own user name
    Given users:
      | name        | status |
      | Fred Bloggs | 1      |
      | Jane Doe    | 1      |
    And I am logged in as "Fred Bloggs"
    When I visit "/"
    Then I should see "Fred Bloggs" in the "User menu" region
    # Check that user name is not cached too long
    Given I am logged in as "Jane Doe"
    When I visit "/"
    Then I should see "Jane Doe" in the "User menu" region

