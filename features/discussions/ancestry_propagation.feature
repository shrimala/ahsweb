@api
Feature: Ancestry that shows a hierarchy based on first parents
  In order to keep organise discussions
  As any user viewing or editing a discussion
  I need to see a lineage of parents that is always up to date.

  Background:
    Given I am logged in as an "authenticated user"
    Given "discussion" content:
      | title        | field_parents |
      | grandparent1 | Discuss       |
    Given "discussion" content:
      | title        | field_parents |
      | grandparent2 | Discuss       |
    Given "discussion" content:
      | title        | field_parents |
      | grandparent3 | Discuss       |
    Given "discussion" content:
      | title       | field_parents |
      | parent1     | grandparent1  |
    Given "discussion" content:
      | title       | field_parents |
      | parent2     | grandparent2  |
    Given "discussion" content:
      | title      | field_parents |
      | child1     | parent1       |
    Given "discussion" content:
      | title      | field_parents |
      | child2     | parent2       |
    Given "discussion" content:
      | title           | field_parents |
      | grandchild1     | child1        |
    Given "discussion" content:
      | title           | field_parents |
      | grandchild2     | child2        |

  Scenario: Ancestry is created
    When I visit "/discuss/grandparent1/parent1/child1/grandchild1"
    Then I should see "Discuss" displayed from the "field_parents" field
    And I should see "grandparent1" displayed from the "field_parents" field
    And I should see "parent1" displayed from the "field_parents" field
    And I should see "child1" displayed from the "field_parents" field

  Scenario: Ancestry changes when first parent's ancestry changes
    When I visit "/discuss/grandparent1/parent1/child1"
    And I fill in "field_parents[0][target_id]" with "parent2"
    And I press the "Save" button
    When I visit "/discuss/grandparent2/parent2/child1/grandchild1"
    Then I should see "Discuss" displayed from the "field_parents" field
    And I should see "grandparent2" displayed from the "field_parents" field
    And I should see "parent2" displayed from the "field_parents" field
    And I should see "child1" displayed from the "field_parents" field
    And I should not see "grandparent1" displayed from the "field_parents" field
    And I should not see "parent1" displayed from the "field_parents" field
    And I should not see "child2" displayed from the "field_parents" field

  Scenario: Ancestry doesn't change when first parent has an additional parent added
    When I visit "/discuss/grandparent1/parent1/child1"
    And I fill in "field_parents[1][target_id]" with "grandparent3"
    And I press the "Save" button
    When I visit "/discuss/grandparent1/parent1/child1/grandchild1"
    Then I should see "Discuss" displayed from the "field_parents" field
    And I should see "grandparent1" displayed from the "field_parents" field
    And I should see "parent1" displayed from the "field_parents" field
    And I should see "child1" displayed from the "field_parents" field
    And I should not see "grandparent3" displayed from the "field_parents" field

  Scenario: Ancestry doesn't change when first parent has changes to secondary parent
    When I visit "/discuss/grandparent1/parent1/child1"
    And I fill in "field_parents[1][target_id]" with "grandparent3"
    And I press the "Save" button
    And I fill in "field_parents[1][target_id]" with "parent2"
    When I visit "/discuss/grandparent1/parent1/child1/grandchild1"
    Then I should see "Discuss" displayed from the "field_parents" field
    And I should see "grandparent1" displayed from the "field_parents" field
    And I should see "parent1" displayed from the "field_parents" field
    And I should see "child1" displayed from the "field_parents" field
    And I should not see "grandparent2" displayed from the "field_parents" field
    And I should not see "parent2" displayed from the "field_parents" field
