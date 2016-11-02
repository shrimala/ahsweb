@api
Feature: Discussions Corresponding Entity References
  In order to set both the parents and children of a discussion from the discussion
  As any user editing a discussion
  I need my changes to be synced to the parents and children

  #These tests assume normal entity reference widget behaviors.
  #They still work even though these fields use the er_enhanced widget,
  #because while that widget uses css/js to hide the form elements,
  #they are still actually there on the non-js browser used here
  #since these steps are not tagged @javascript.

  Background:
    Given I am logged in as an administrator

  Scenario: Creating a parent with a child reference sets the parent reference on the child
    Given a discussion content with the title "child1"
    Given "discussion" content:
      | title       | field_children |
      | parent1     | child1         |
    When I am at "/admin/content"
    And I click "parent1"
    Then I should see "child1" displayed from the "field_children" field
    When I click "child1"
    Then I should see "parent1" displayed from the "field_parents" field

  Scenario: Creating a parent with 2 child references sets the parent reference on both children
    Given a discussion content with the title "child1"
    Given a discussion content with the title "child2"
    Given "discussion" content:
      | title       | field_children |
      | parent1     | child1, child2 |
    When I am at "/admin/content"
    When I click "parent1"
    Then I should see "child1" displayed from the "field_children" field
    When I click "child1"
    Then I should see "parent1" displayed from the "field_parents" field
    When I am at "/admin/content"
    When I click "parent1"
    Then I should see "child2" displayed from the "field_children" field
    When I click "child2"
    Then I should see "parent1" displayed from the "field_parents" field

  Scenario: Creating a parent with a child reference sets the parent reference on the child even if it already has a parent
    Given a "discussion" content with the title "child1"
    Given "discussion" content:
      | title       | field_children |
      | parent1     | child1         |
      | parent2     | child1         |
    When I am at "/admin/content"
    When I click "parent1"
    Then I should see "child1" displayed from the "field_children" field
    When I click "child1"
    Then I should see "parent1" displayed from the "field_parents" field
    When I am at "/admin/content"
    When I click "parent2"
    Then I should see "child1" displayed from the "field_children" field
    When I click "child1"
    Then I should see "parent2" displayed from the "field_parents" field

  Scenario: Deleting a parent reference on the child deletes the child reference on the parent
    Given a discussion content with the title "child1"
    Given "discussion" content:
      | title       | field_children |
      | parent1     | child1         |
    Given "discussion" content:
      | title       | field_children |
      | parent2     | child1         |
    When I am at "/admin/content"
    When I click "child1"
    And I empty the field "field_parents[0][target_id]"
    And I press the "Save" button
    Then I should not see "parent1" displayed from the "field_parents" field
    Then I should see "parent2" displayed from the "field_parents" field
    When I am at "/admin/content"
    And I click "parent1"
    Then I should not see "child1" displayed from the "field_children" field
    When I am at "/admin/content"
    And I click "parent2"
    Then I should see "child1" displayed from the "field_children" field

  Scenario: Deleting a child reference on the parent deletes the parent reference on the child
    Given a discussion content with the title "child1"
    Given "discussion" content:
      | title       | field_children |
      | parent1     | child1         |
    When I am at "/admin/content"
    When I click "parent1"
    And I empty the field "field_children[0][target_id]"
    And I press the "Save" button
    Then I should not see "child1" displayed from the "field_children" field
    When I am at "/admin/content"
    And I click "child1"
    Then I should not see "parent1" displayed from the "field_parents" field

  Scenario: Updating a parent with a new child reference sets the parent reference on the child, also if autocreated
    Given a discussion content with the title "child1"
    Given a discussion content with the title "child2"
    Given "discussion" content:
      | title       | field_children |
      | parent1     | child1         |
    When I am at "/admin/content"
    When I click "parent1"
    When I fill in "field_children[0][target_id]" with "child2"
    And I fill in "field_children[1][target_id]" with "cer_autocreate_notcleanedup"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "parent1"
    Then I should not see "child1" displayed from the "field_children" field
    And I should see "child2" displayed from the "field_children" field
    And I should see "cer_autocreate_notcleanedup" displayed from the "field_children" field
    When I am at "/admin/content"
    And I click "child1"
    Then I should not see "parent1" displayed from the "field_parents" field
    When I am at "/admin/content"
    And I click "child2"
    Then I should see "parent1" displayed from the "field_parents" field
    When I am at "/admin/content"
    And I click "cer_autocreate_notcleanedup"
    Then I should see "parent1" displayed from the "field_parents" field

  Scenario: Updating a child with a new parent reference sets the child reference on the parent
    Given "discussion" content:
      | title       |
      | parent1     |
      | parent2     |
    Given "discussion" content:
      | title      | field_parents   |
      | child1     | parent1         |
    When I am at "/admin/content"
    When I click "child1"
    When I fill in "field_parents[0][target_id]" with "parent2"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "child1"
    Then I should not see "parent1" displayed from the "field_parents" field
    And I should see "parent2" displayed from the "field_parents" field
    When I am at "/admin/content"
    And I click "parent1"
    Then I should not see "child1" displayed from the "field_children" field
    When I am at "/admin/content"
    And I click "parent2"
    Then I should see "child1" displayed from the "field_children" field

  Scenario: Can add a parent reference when there aren't any
    Given "discussion" content:
      | title       |
      | parent1     |
    Given a discussion content with the title "child1"
    When I am at "/admin/content"
    When I click "child1"
    When I fill in "field_parents[0][target_id]" with "parent1"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "parent1"
    Then I should see "child1" displayed from the "field_children" field

  Scenario: Cannot autocreate a parent reference
    Given a discussion content with the title "child1"
    When I am at "/admin/content"
    And I click "child1"
    And I fill in "field_parents[0][target_id]" with "autocreated_should_not_exist"
    And I press the "Save" button
    When I am at "/admin/content"
    Then I should not see "autocreated_should_not_exist"
