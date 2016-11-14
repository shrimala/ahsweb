@api
Feature: Ancestry that shows a hierarchy based on first parents
  In order to keep organise discussions
  As any user viewing or editing a discussion
  I need to see a lineage of parents that is always up to date.

  Background:
    Given I am logged in as an "authenticated user"
    Given "discussion" content:
      | title        |
      | Grandparent1 |
    Given "discussion" content:
      | title        |
      | Grandparent2 |
    Given "discussion" content:
      | title        |
      | Grandparent3 |
    Given "discussion" content:
      | title       | field_parents |
      | Parent1     | Grandparent1  |
    Given "discussion" content:
      | title       | field_parents |
      | Parent2     | Grandparent2  |
    Given "discussion" content:
      | title      | field_parents |
      | Child1     | Parent1       |
    Given "discussion" content:
      | title      | field_parents |
      | Child2     | Parent2       |
    Given "discussion" content:
      | title           | field_parents |
      | Grandchild1     | Child1        |
    Given "discussion" content:
      | title           | field_parents |
      | Grandchild2     | Child2        |

  Scenario: Ancestry is created
    When I visit "/discuss/grandparent1/parent1/child1/grandchild1"
    Then I should see "Home" displayed from the "field_parents" field
    And I should see "Grandparent1" displayed from the "field_parents" field
    And I should see "Parent1" displayed from the "field_parents" field
    And I should see "Child1" displayed from the "field_parents" field

  Scenario: Ancestry changes when first parent's ancestry changes
    When I visit "/discuss/grandparent1/parent1/child1"
    And I fill in "field_parents[0][target_id]" with "Parent2"
    And I press the "Save" button
    When I visit "/discuss/grandparent2/parent2/child1/grandchild1"
    Then I should see "Home" displayed from the "field_parents" field
    And I should see "Grandparent2" displayed from the "field_parents" field
    And I should see "Parent2" displayed from the "field_parents" field
    And I should see "Child1" displayed from the "field_parents" field
    And I should not see "Grandparent1" displayed from the "field_parents" field
    And I should not see "Parent1" displayed from the "field_parents" field
    And I should not see "Child2" displayed from the "field_parents" field

  Scenario: Ancestry doesn't change when first parent has an additional parent added
    When I visit "/discuss/grandparent1/parent1/child1"
    And I fill in "field_parents[1][target_id]" with "Grandparent3"
    And I press the "Save" button
    When I visit "/discuss/grandparent1/parent1/child1/grandchild1"
    Then I should see "Home" displayed from the "field_parents" field
    And I should see "Grandparent1" displayed from the "field_parents" field
    And I should see "Parent1" displayed from the "field_parents" field
    And I should see "Child1" displayed from the "field_parents" field
    And I should not see "Grandparent3" displayed from the "field_parents" field

  Scenario: Ancestry doesn't change when first parent has changes to secondary parent
    When I visit "/discuss/grandparent1/parent1/child1"
    And I fill in "field_parents[1][target_id]" with "Grandparent3"
    And I press the "Save" button
    And I fill in "field_parents[1][target_id]" with "Parent2"
    When I visit "/discuss/grandparent1/parent1/child1/grandchild1"
    Then I should see "Home" displayed from the "field_parents" field
    And I should see "Grandparent1" displayed from the "field_parents" field
    And I should see "Parent1" displayed from the "field_parents" field
    And I should see "Child1" displayed from the "field_parents" field
    And I should not see "Grandparent2" displayed from the "field_parents" field
    And I should not see "Parent2" displayed from the "field_parents" field
