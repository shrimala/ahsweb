@api @javascript @skip
Feature: Keep track of what users have read
  In order to call attention to unseen changes and comments on discussion
  As a user viewing lists of discussions
  I should see 'New' flags against those with unseen changes or comments.

  Background:
    Given users:
      | name        | status |
      | Fred Bloggs  | 1      |
      | Jane Doe     | 1      |

  Scenario: Visiting discussions makes them read, other people commenting makes them unread
    Given "discussion" content:
      | title       |
      | Testtitle1  |
      | Testtitle2  |
      | Testtitle3  |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    Then I should see "Active discussions":
      | Title field | .ahs-new |
      | Testtitle1  | Yes      |
      | Testtitle2  | Yes      |
      | Testtitle3  | Yes      |
    When I visit "/discuss/testtitle1"
    And I visit "/discuss/testtitle3"
    When I visit "/"
    Then I should see "Active discussions":
      | Title field | .ahs-new |
      | Testtitle1  | No       |
      | Testtitle2  | Yes      |
      | Testtitle3  | No       |
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/testtitle1"
    And I comment "Some comment"
    Given I am logged in as "Jane Doe"
    When I visit "/"
    Then I should see "Active discussions":
      | Title field | .ahs-new |
      | Testtitle1  | Yes      |
      | Testtitle2  | Yes      |
      | Testtitle3  | No       |
    When I visit "/discuss/testtitle1"
    And I visit "/"
    Then I should see "Active discussions":
      | Title field | .ahs-new |
      | Testtitle1  | No       |
      | Testtitle2  | Yes      |
      | Testtitle3  | No       |

  Scenario: Discussion is marked as read, even when listing is served from cache
    Given "discussion" content:
      | title       |
      | Testtitle1  |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    Then I should see "Active discussions":
      | Title field | .ahs-new |
      | Testtitle1  | Yes      |
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/testtitle1"
    And I visit "/"
    Then I should see "Active discussions":
      | Title field | .ahs-new |
      | Testtitle1  | No       |

  Scenario: New marks are present on discussion children listings
    Given "discussion" content:
      | title        |
      | Grandparent  |
    And "discussion" content:
      | title  | field_parents |
      | Parent | Grandparent   |
    And "discussion" content:
      | title | field_parents |
      | Child | Parent        |
    And I am logged in as "Fred Bloggs"
    When I visit "/discuss/grandparent/parent"
    Then I should see "Discussion parents":
      | Title field | .ahs-new |
      | Grandparent | Yes      |
    Then I should see "Discussion children":
      | Title field | .ahs-new |
      | Child       | Yes      |
    When I visit "/discuss/grandparent"
    And I visit "/discuss/grandparent/parent/child"
    When I visit "/discuss/grandparent/parent"
    Then I should see "Discussion parents":
      | Title field | .ahs-new |
      | Grandparent | No       |
    Then I should see "Discussion children":
      | Title field | .ahs-new |
      | Child       | No       |
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/grandparent"
    And I comment "Some comment"
    And I visit "/discuss/grandparent/parent/child"
    And I comment "Some comment"
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/grandparent/parent"
    Then I should see "Discussion parents":
      | Title field | .ahs-new |
      | Grandparent | Yes      |
    Then I should see "Discussion children":
      | Title field | .ahs-new |
      | Child       | Yes      |

  Scenario: Autocreated children are not new
    Given I am logged in as an "authenticated user"
    And a "discussion" with the title "Test"
    When I visit "/discuss/test"
    When I fill in "field_children[0][target_id]" with "Child"
    And I press the "Save" button
    Then I should see "Discussion children":
      | Title field | .ahs-new |
      | Child       | No       |

  Scenario: Removing a parent doesn't mark it as new
    Given I am logged in as an "authenticated user"
    And a discussion content with the title "Child"
    And "discussion" content:
      | title       | field_children |
      | Parent      | Child          |
    When I visit "/discuss/parent"
    And I visit "/discuss/parent/child"
    And I empty the field "field_parents[0][target_id]"
    And I visit "/"
    Then I should see "Active discussions":
      | Title field | .ahs-new |
      | Child       | No       |
      | Parent      | No       |

  Scenario: Adding a child doesn't mark it as new
    Given I am logged in as an "authenticated user"
    And a discussion content with the title "Parent"
    And a discussion content with the title "Child"
    When I visit "/discuss/child"
    And I visit "/discuss/parent"
    When I fill in "field_children[0][target_id]" with "[Child]"
    And I press "Save"
    Then I should see "Discussion children":
      | Title field | .ahs-new |
      | Child       | No       |
