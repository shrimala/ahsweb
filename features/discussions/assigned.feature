@api
Feature: Mark which users are assigned a discussion (as a task)
  In order to highlight tasks to the users supposed to be doing them
  As any user allowed to edit a discussion
  I need to see and edit a list of users the discussion has been assigned to.

  Background:
    Given users:
      | name         | status |
      | Fred Bloggs  | 1      |
      | Jane Doe     | 1      |

  Scenario: Assign and unassign oneself
    Given a "discussion" with the title "Test"
    And I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I select "Jane Doe" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Jane Doe" selected
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/test"
    And I additionally select "Fred Bloggs" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs, Jane Doe" selected
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I select "Fred Bloggs" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs" selected

  Scenario: Assign and unassign someone else
    Given a "discussion" with the title "Test"
    And I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I select "Fred Bloggs" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs" selected
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/test"
    And I additionally select "Jane Doe" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs, Jane Doe" selected
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I select "Jane Doe" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Jane Doe" selected
