@api
Feature: New discussions are cleaned up automatically at scenario end
  In order to have isolated scenarios with predictable results
  As a scenario developer
  I need all discussions created during a scenario to be deleted at its end.

  Scenario: Discussion is created by an anonymous "Given"
    Given a discussion with the title "Test1"

  Scenario: Discussion is cleaned up
    Given I am logged in as an "administrator"
    When I visit "/admin/content"
    Then I should not see "Test1"

  Scenario: Discussion is created with author by a "Given"
    Given I am logged in as an "authenticated user"
    And I create a "discussion" with the title "Test2"

  Scenario: Discussion is cleaned up
    Given I am logged in as an "administrator"
    When I visit "/admin/content"
    Then I should not see "Test2"

  Scenario: Discussion is created manually
    Given I am logged in as an "authenticated user"
    When I visit "/discuss/add"
    And I fill in "title[0][value]" with "Test3"
    And I press the "Save" button

  Scenario: Discussion is cleaned up
    Given I am logged in as an "administrator"
    When I visit "/admin/content"
    Then I should not see "Test3"

  Scenario: Discussion is autocreated
    Given I am logged in as an "authenticated user"
    And a discussion with the title "Parent1"
    When I visit "/discuss/parent1"
    And I fill in "field_children[0][target_id]" with "Child1"
    And I press the "Save" button

  Scenario: Discussion is cleaned up
    Given I am logged in as an "administrator"
    When I visit "/admin/content"
    Then I should not see "Parent1"
    And I should not see "Child1"

  Scenario: Discussion is created manually with child
    Given I am logged in as an "authenticated user"
    When I visit "/discuss/add"
    And I fill in "title[0][value]" with "Parent2"
    And I fill in "field_children[0][target_id]" with "Child2"
    And I press the "Save" button

  Scenario: Discussion is cleaned up
    Given I am logged in as an "administrator"
    When I visit "/admin/content"
    Then I should not see "Parent2"
    And I should not see "Child2"