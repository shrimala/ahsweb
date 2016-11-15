@api
Feature: Discussions ER enhanced widgets for parents & children
  In order to both navigate and edit parents & children fields
  As any user viewing or editing a discussion
  I need to have linked previews.

  Background:
    Given I am logged in as an "authenticated user"
    Given a discussion content with the title "Child2"
    Given a discussion content with the title "Grandchild1"
    Given a discussion content with the title "Grandchild2"
    Given "discussion" content:
      | title      | field_children           |
      | Child1     | Grandchild1, Grandchild2 |
    Given "discussion" content:
      | title       | field_children |
      | Parent1     | Child1, Child2 |
    Given "discussion" content:
      | title       | field_children |
      | Parent2     | Child1         |

  Scenario: Navigate between parents, children and grandchildren
    When I visit "/discuss/parent1/child1"
    Then I should see "Home" displayed from the "field_parents" field
    And I should see "Parent1" displayed from the "field_parents" field
    And I should see "Parent2" displayed from the "field_parents" field
    And I should see "Grandchild1" displayed from the "field_children" field
    And I should see "Grandchild2" displayed from the "field_children" field
    When I click "Parent1"
    Then I am visiting "/discuss/parent1"
    And I should see "Child1" displayed from the "field_children" field
    And I should see "Child2" displayed from the "field_children" field
    And I should see "Home" displayed from the "field_parents" field
    When I click "Child1"
    Then I am visiting "/discuss/parent1/child1"
    When I click "Grandchild1"
    Then I am visiting "/discuss/parent1/child1/grandchild1"
    And I should see "Home" displayed from the "field_parents" field
    And I should see "Parent1" displayed from the "field_parents" field
    And I should see "Child1" displayed from the "field_parents" field
    When I click "Child1"
    Then I am visiting "/discuss/parent1/child1"
    When I click "Grandchild1"
    Then I am visiting "/discuss/parent1/child1/grandchild1"
    When I click "Parent1"
    Then I am visiting "/discuss/parent1"
    When I click "Child1"
    Then I am visiting "/discuss/parent1/child1"
    When I click "Parent2"
    Then I am visiting "/discuss/parent2"
    When I click "Child1"
    Then I am visiting "/discuss/parent1/child1"
    When I click "Grandchild2"
    Then I am visiting "/discuss/parent1/child1/grandchild2"

  Scenario: Links to home page work
    When I visit "/discuss/parent1"
    Then I should see "Home" displayed from the "field_parents" field
    When I click "Home"
    Then I am visiting "/"
    When I visit "/discuss/parent1/child1"
    Then I should see "Home" displayed from the "field_parents" field
    When I click "Home"
    Then I am visiting "/"