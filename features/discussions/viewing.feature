@api
Feature: Ancestry that shows a hierarchy based on first parents
  In order to easily add new discussions
  As any user allowed to create a discussion
  I need to have a form for creating a new discussion.

  Background:
    Given I am logged in as an "authenticated user"

  Scenario: Autocomplete typed in exact matches work
    Given "discussion" content:
      | title   |
      | parent  |
    Given "discussion" content:
      | title   | field_parents |
      | dog     | parent        |
    Given a discussion content with the title "dog cat"
    When I visit "/discuss/add"
    And I fill in "title[0][value]" with "testtitle"
    And I fill in "field_parents[0][target_id]" with "parent"
    And I fill in "field_children[0][target_id]" with "dog"
    And I press the "Save" button
    Then I am visiting "/discuss/parent/testtitle"
    And I should see "dog" displayed from the "field_children" field
    When I click "dog"
    Then I am visiting "/discuss/parent/dog"
