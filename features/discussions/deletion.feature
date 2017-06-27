@api
Feature: Discussions can be deleted by discussion admin
  In order to get rid of unnecessary discussions
  As a user who is moderating discussions
  I need to be able to delete discussions

  Scenario: Deleting a discussion
    Given I am logged in as an "authenticated user"
    And a "discussion" with the title "test discussion"
    Then I should not see "Delete"
    Given I am logged in as a "Discussion admin"
    And I visit "/"
    Then I should see "test discussion"
    When I visit "/discuss/test-discussion"
    Then I should see "Delete"
    When I follow "Delete"
    And I press the "Delete" button
    And I visit "/"
    Then I should not see "test discussion"
