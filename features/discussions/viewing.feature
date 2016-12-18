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
      | Parent  |
    Given "discussion" content:
      | title   | field_parents |
      | Dog     | Parent        |
    Given a discussion content with the title "Dog cat"
    When I visit "/discuss/add"
    And I fill in "title[0][value]" with "Testtitle"
    And I fill in "field_parents[0][target_id]" with "Parent"
    And I fill in "field_children[0][target_id]" with "[Dog]"
    And I press the "Save" button
    Then I am visiting "/discuss/parent/testtitle"
    And I should see "Dog" displayed from the "field_children" field
    When I click "Dog"
    Then I am visiting "/discuss/parent/dog"
