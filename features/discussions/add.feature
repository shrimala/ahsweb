@api
Feature: Ancestry that shows a hierarchy based on first parents
  In order to easily add new discussions
  As any user allowed to create a discussion
  I need to have a form for creating a new discussion.

  Background:
    Given I am logged in as an "authenticated user"

  Scenario: Discussion is created
    When I visit "/discuss/add"
    And I fill in "title[0][value]" with "testtitle"
    And I press the "Save" button
    Then I am visiting "/discuss/testtitle"
    And the "title[0][value]" field should contain "testtitle"

  Scenario: Discussion is created, existing parents & children can be referenced
    Given "discussion" content:
      | title      |
      | parent     |
    Given a discussion content with the title "grandchild"
    When I visit "/discuss/add"
    When I fill in "title[0][value]" with "child"
    And I fill in "field_parents[0][target_id]" with "parent"
    And I fill in "field_children[0][target_id]" with "grandchild"
    And I press the "Save" button
    Then I am visiting "/discuss/parent/child"
    And I should see "grandchild" displayed from the "field_children" field
    When I click "grandchild"
    Then I am visiting "/discuss/parent/child/grandchild"
    When I visit "/discuss/parent/child"
    Then I should see "parent" displayed from the "field_parents" field
    When I click "parent"
    Then I am visiting "/discuss/parent"

  Scenario: Discussion is created, child is autocreated
    When I visit "/discuss/add"
    When I fill in "title[0][value]" with "testtitle"
    When I fill in "field_children[0][target_id]" with "child"
    And I press the "Save" button
    Then I am visiting "/discuss/testtitle"
    And I should see "child" displayed from the "field_children" field
    When I click "child"
    Then I am visiting "/discuss/testtitle/child"

