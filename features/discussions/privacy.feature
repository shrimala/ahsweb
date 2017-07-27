@api
Feature: Private discussions
  In order to have private discussions
  As any user allowed to participate in discussions
  I should be able to restrict access to a discussion to participants only.

  Background:
    Given users:
      | name        | status |
      | Fred Bloggs  | 1      |
      | Jane Doe     | 1      |
      | Hans Schmidt | 1      |

  Scenario: Discussion can be set to private
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/add"
    Then the "Private" checkbox should not be checked
    When I fill in "title[0][value]" with "Testtitle"
    And I check "Private"
    And I press the "Save" button
    Then I am visiting "/discuss/testtitle"
    And the "Private" checkbox should be checked

  @ignoreDrupalErrors
  Scenario: Anonymous cannot access discussions of any kind
    Given "discussion" content:
      | title      | field_participants         | field_private |
      | test title1 | Fred Bloggs                | 0             |
      | test title2 | Fred Bloggs                | 1             |
    Given I am not logged in
    When I go to "/discuss/test-title1"
    Then I am visiting "/user/login"
    And I should see the error message "Access denied. You must log in to view this page"
    And I should not see "test title1"
    When I go to "/discuss/test-title2"
    Then I am visiting "/user/login"
    And I should see the error message "Access denied. You must log in to view this page"
    And I should not see "test title2"

  @ignoreDrupalWarnings
  Scenario: Non-participants cannot access private discussions
    Given "discussion" content:
      | title       | body      | field_participants    | field_private |
      | test title1 | body text | Fred Bloggs           | 1             |
      | test title2 | body text | Fred Bloggs, Jane Doe | 1             |
      | test title3 | body text | Fred Bloggs, Jane Doe | 0             |
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/test-title1"
    Then the "Title" field should contain "test title1"
    And I should see "body text"
    When I visit "/discuss/test-title2"
    Then the "Title" field should contain "test title2"
    And I should see "body text"
    When I visit "/discuss/test-title3"
    Then the "Title" field should contain "test title3"
    And I should see "body text"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test-title1"
    Then I should see the warning message "Sorry, this discussion is private"
    And I should not see "body text"
    When I visit "/discuss/test-title2"
    Then the "Title" field should contain "test title2"
    And I should see "body text"
    Given I am logged in as "Hans Schmidt"
    When I visit "/discuss/test-title1"
    Then I should see the warning message "Sorry, this discussion is private"
    And I should not see "body text"
    When I visit "/discuss/test-title2"
    Then I should see the warning message "Sorry, this discussion is private"
    And I should not see "body text"
    When I visit "/discuss/test-title3"
    Then the "Title" field should contain "test title3"
    And I should see "body text"