@api
Feature: Ancestry that shows a hierarchy based on first parents
  In order to easily add new discussions
  As any user allowed to create a discussion
  I need to have a form for creating a new discussion.

  Background:
    Given I am logged in as an "authenticated user"

  Scenario: Discussion is created
    When I visit "/discuss/add"
    And I fill in "title[0][value]" with "Testtitle"
    And I press the "Save" button
    Then I am visiting "/discuss/testtitle"
    And the "title[0][value]" field should contain "Testtitle"

  Scenario: Discussion is created, existing parents & children can be referenced
    Given "discussion" content:
      | title      |
      | Parent     |
    Given a discussion content with the title "Grandchild"
    When I visit "/discuss/add"
    When I fill in "title[0][value]" with "Child"
    And I fill in "field_parents[0][target_id]" with "Parent"
    And I fill in "field_children[0][target_id]" with "[Grandchild]"
    And I press the "Save" button
    Then I am visiting "/discuss/parent/child"
    And I should see "Grandchild" displayed from the "field_children" field
    When I click "Grandchild"
    Then I am visiting "/discuss/parent/child/grandchild"
    When I visit "/discuss/parent/child"
    Then I should see "Parent" displayed from the "field_parents" field
    When I click "Parent"
    Then I am visiting "/discuss/parent"

  Scenario: Discussion is created, child is autocreated
    When I visit "/discuss/add"
    When I fill in "title[0][value]" with "Testtitle"
    When I fill in "field_children[0][target_id]" with "Child"
    And I press the "Save" button
    Then I am visiting "/discuss/testtitle"
    And I should see "Child" displayed from the "field_children" field
    When I click "Child"
    Then I am visiting "/discuss/testtitle/child"

