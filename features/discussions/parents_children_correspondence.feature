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
    Given a discussion content with the title "Child1"
    Given "discussion" content:
      | title       | field_children |
      | Parent1     | Child1         |
    When I am at "/admin/content"
    And I click "Parent1"
    Then I should see "Child1" displayed from the "field_children" field
    When I click "Child1"
    Then I should see "Parent1" displayed from the "field_parents" field

  Scenario: Creating a parent with 2 child references sets the parent reference on both children
    Given a discussion content with the title "Child1"
    Given a discussion content with the title "Child2"
    Given "discussion" content:
      | title       | field_children |
      | Parent1     | Child1, Child2 |
    When I am at "/admin/content"
    When I click "Parent1"
    Then I should see "Child1" displayed from the "field_children" field
    When I click "Child1"
    Then I should see "Parent1" displayed from the "field_parents" field
    When I am at "/admin/content"
    When I click "Parent1"
    Then I should see "Child2" displayed from the "field_children" field
    When I click "Child2"
    Then I should see "Parent1" displayed from the "field_parents" field

  Scenario: Creating a parent with a child reference sets the parent reference on the child even if it already has a parent
    Given a "discussion" content with the title "Child1"
    Given "discussion" content:
      | title       | field_children |
      | Parent1     | Child1         |
      | Parent2     | Child1         |
    When I am at "/admin/content"
    When I click "Parent1"
    Then I should see "Child1" displayed from the "field_children" field
    When I click "Child1"
    Then I should see "Parent1" displayed from the "field_parents" field
    When I am at "/admin/content"
    When I click "Parent2"
    Then I should see "Child1" displayed from the "field_children" field
    When I click "Child1"
    Then I should see "Parent2" displayed from the "field_parents" field

  Scenario: Deleting a parent reference on the child deletes the child reference on the parent
    Given a discussion content with the title "Child1"
    Given "discussion" content:
      | title       | field_children |
      | Parent1     | Child1         |
    Given "discussion" content:
      | title       | field_children |
      | Parent2     | Child1         |
    When I am at "/admin/content"
    When I click "Child1"
    And I empty the field "field_parents[0][target_id]"
    And I press the "Save" button
    Then I should not see "Parent1" displayed from the "field_parents" field
    Then I should see "Parent2" displayed from the "field_parents" field
    When I am at "/admin/content"
    And I click "Parent1"
    Then I should not see "Child1" displayed from the "field_children" field
    When I am at "/admin/content"
    And I click "Parent2"
    Then I should see "Child1" displayed from the "field_children" field

  Scenario: Deleting a child reference on the parent deletes the parent reference on the child
    Given a discussion content with the title "Child1"
    Given "discussion" content:
      | title       | field_children |
      | Parent1     | Child1         |
    When I am at "/admin/content"
    When I click "Parent1"
    And I empty the field "field_children[0][target_id]"
    And I press the "Save" button
    Then I should not see "Child1" displayed from the "field_children" field
    When I am at "/admin/content"
    And I click "Child1"
    Then I should not see "Parent1" displayed from the "field_parents" field

  Scenario: Updating a parent with a new child reference sets the parent reference on the child, also if autocreated
    Given a discussion content with the title "Child1"
    Given a discussion content with the title "Child2"
    Given "discussion" content:
      | title       | field_children |
      | Parent1     | Child1         |
    When I am at "/admin/content"
    When I click "Parent1"
    When I fill in "field_children[0][target_id]" with "[Child2]"
    And I fill in "field_children[1][target_id]" with "Cer_autocreate_notcleanedup"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "Parent1"
    Then I should not see "Child1" displayed from the "field_children" field
    And I should see "Child2" displayed from the "field_children" field
    And I should see "Cer_autocreate_notcleanedup" displayed from the "field_children" field
    When I am at "/admin/content"
    And I click "Child1"
    Then I should not see "Parent1" displayed from the "field_parents" field
    When I am at "/admin/content"
    And I click "Child2"
    Then I should see "Parent1" displayed from the "field_parents" field
    When I am at "/admin/content"
    And I click "Cer_autocreate_notcleanedup"
    Then I should see "Parent1" displayed from the "field_parents" field

  Scenario: Updating a child with a new parent reference sets the child reference on the parent
    Given "discussion" content:
      | title       |
      | Parent1     |
      | Parent2     |
    Given "discussion" content:
      | title      | field_parents   |
      | Child1     | Parent1         |
    When I am at "/admin/content"
    When I click "Child1"
    When I fill in "field_parents[0][target_id]" with "Parent2"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "Child1"
    Then I should not see "Parent1" displayed from the "field_parents" field
    And I should see "Parent2" displayed from the "field_parents" field
    When I am at "/admin/content"
    And I click "Parent1"
    Then I should not see "Child1" displayed from the "field_children" field
    When I am at "/admin/content"
    And I click "Parent2"
    Then I should see "Child1" displayed from the "field_children" field

  Scenario: Can add a parent reference when there aren't any
    Given "discussion" content:
      | title       |
      | Parent1     |
    Given a discussion content with the title "Child1"
    When I am at "/admin/content"
    When I click "Child1"
    When I fill in "field_parents[0][target_id]" with "Parent1"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "Parent1"
    Then I should see "Child1" displayed from the "field_children" field

  Scenario: Cannot autocreate a parent reference
    Given a discussion content with the title "Child1"
    When I am at "/admin/content"
    And I click "Child1"
    And I fill in "field_parents[0][target_id]" with "Autocreated_should_not_exist"
    And I press the "Save" button
    When I am at "/admin/content"
    Then I should not see "Autocreated_should_not_exist"
