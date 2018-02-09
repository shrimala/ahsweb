@api
Feature: Miscellaneous aspects of viewing discussions

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

  Scenario: Revision log is hidden
    Given a "discussion" with the title "test"
    When I visit "/discuss/test"
    Then I should not see a ".field--name-revision-log" element

  Scenario: Redirect when visiting old titles
    Given "discussion" content:
      | title   |
      | Parent  |
    Given "discussion" content:
      | title   | field_parents |
      | Dog     | Parent        |
    When I visit "/discuss/parent/dog"
    Then the "title[0][value]" field should contain "dog"
    When I fill in "title[0][value]" with "cat"
    And I press the "Save" button
    Then I am visiting "/discuss/parent/cat"
    And the "title[0][value]" field should contain "cat"
    When I visit "/discuss/parent/dog"
    Then I am visiting "/discuss/parent/cat"
    And the "title[0][value]" field should contain "cat"


