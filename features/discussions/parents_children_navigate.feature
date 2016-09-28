@api
Feature: Discussions ER enhanced widgets for parents & children
  In order to both navigate and edit parents & children fields
  As any user viewing or editing a discussion
  I need to have linked previews.

  Background:
    Given I am logged in as an "authenticated user"
    Given a discussion content with the title "child2"
    Given a discussion content with the title "grandchild1"
    Given a discussion content with the title "grandchild2"
    Given "discussion" content:
      | title       | field_children          |
      | child1     | grandchild1, grandchild2 |
    Given "discussion" content:
      | title       | field_children |
      | parent1     | child1, child2  |
    Given "discussion" content:
      | title       | field_children |
      | parent2     | child1         |

  Scenario: Navigate between parents, children and grandchildren
    When I visit "discuss/parent1/child1"
    Then I should see "Discuss" displayed from the "field_parents" field
    And I should see "parent1" displayed from the "field_parents" field
    And I should see "parent2" displayed from the "field_parents" field
    And I should see "grandchild1" displayed from the "field_children" field
    And I should see "grandchild2" displayed from the "field_children" field
    When I click "parent1"
    Then I am on "discuss/parent1"
    And I should see "child1" displayed from the "field_children" field
    And I should see "child2" displayed from the "field_children" field
    And I should see "Discuss" displayed from the "field_parents" field
    When I click "child1"
    Then I am on "discuss/child1"
    When I click "grandchild1"
    Then I am on "discuss/parent1/child1/grandchild1"
    And I should see "Discuss" displayed from the "field_parents" field
    And I should see "parent1" displayed from the "field_parents" field
    And I should see "child1" displayed from the "field_parents" field
    When I click "child1"
    Then I am on "discuss/child1"
    When I click "grandchild1"
    Then I am on "discuss/parent1/child1/grandchild1"
    When I click "parent1"
    Then I am on "discuss/parent1"
    When I click "child1"
    Then I am on "discuss/child1"
    When I click "parent2"
    Then I am on "discuss/parent2"
    When I click "child1"
    Then I am on "discuss/child1"
    When I click "grandchild2"
    Then I am on "discuss/parent1/child1/grandchild2"