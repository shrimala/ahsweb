@api
Feature: Mark which users are assigned a discussion (as a task)
  In order to highlight tasks to the users supposed to be doing them
  As any user allowed to edit a discussion
  I need to see and edit a list of users the discussion has been assigned to.

  Background:
    Given users:
      | name         | mail             | status |
      | Fred Bloggs  | fred@example.com | 1      |
      | Jane Doe     | jane@example.com | 1      |
      | Hans Schmidt | hans@example.com | 1      |

  Scenario: Assign and unassign oneself
    Given a "discussion" with the title "Test"
    And I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I select "Jane Doe" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Jane Doe" selected
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/test"
    And I additionally select "Fred Bloggs" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs, Jane Doe" selected
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I select "Fred Bloggs" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs" selected
    Then no email has been sent

  Scenario: Assign and unassign someone else
    Given a "discussion" with the title "Test"
    And I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I select "Fred Bloggs" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs" selected
    And a new email is sent:
      | to   | subject        | body     |
      | Fred | Assigned: Test | Jane Doe |
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/test"
    And I additionally select "Jane Doe" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs, Jane Doe" selected
    And a new email is sent:
      | to   | subject        | body        |
      | Jane | Assigned: Test | Fred Bloggs |
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    # Implicitly unassign Fred Bloggs
    And I select "Jane Doe" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Jane Doe" selected
    And a new email is sent:
      | to   | subject          | body     |
      | Fred | Unassigned: Test | Jane Doe |

  Scenario: Assign 2 people sequentially
    Given a "discussion" with the title "Test"
    And I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I select "Fred Bloggs" from "field_assigned[]"
    And I press "Save"
    And I additionally select "Hans Schmidt" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs, Hans Schmidt" selected
    And new emails are sent:
      | to   | subject        | body     |
      | Fred | Assigned: Test | Jane Doe |
      | Hans | Assigned: Test | Jane Doe |
    When I select "Jane Doe" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Jane Doe" selected
    And new emails are sent:
      | to   | subject          | body     |
      | Fred | Unassigned: Test | Jane Doe |
      | Hans | Unassigned: Test | Jane Doe |

  Scenario: Assign 2 people simultaneously
    Given a "discussion" with the title "Test"
    And I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I select "Fred Bloggs" from "field_assigned[]"
    And I additionally select "Hans Schmidt" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Fred Bloggs, Hans Schmidt" selected
    And new emails are sent:
      | to   | subject        | body     |
      | Fred | Assigned: Test | Jane Doe |
      | Hans | Assigned: Test | Jane Doe |

  Scenario: Unassignment notification not sent for finished discussions
    Given "discussion" content:
      | title | field_assigned | field_finished |
      | test  | Fred Bloggs    | 1              |
    Then a new email is sent:
      | to   | subject        |
      | Fred | Assigned: Test |
    And I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    Then the "field_assigned[]" select field should have "Fred Bloggs" selected
    # Implicitly unassign Fred Bloggs
    And I select "Jane Doe" from "field_assigned[]"
    And I press "Save"
    Then the "field_assigned[]" select field should have "Jane Doe" selected
    And no new email is sent

  Scenario: (Un)Assigned notifications have useful content
    Given "discussion" content:
      | title       | field_parents | field_assigned | body                |
      | cousin      |               |                |                     |
      | grandparent |               |                |                     |
      | parent      | grandparent   |                |                     |
      | child       | parent        | Fred Bloggs    | Some body text here |
    Then a new email is sent:
      | to   | subject         | body                     | body  | body           |
      | Fred | Assigned: Child | grandparent / parent     | child | Some body text |
    Given I am logged in as "Fred Bloggs"
    When I follow the link to "/discuss/" from the email to Fred
    Then I am visiting "/discuss/grandparent/parent/child"
    And I should see "Some body text here"
    Given I am logged in as "Jane Doe"
    And I visit "/discuss/grandparent/parent/child"
    And I select "Jane Doe" from "field_assigned[]"
    And I fill in "field_parents[0][target_id]" with "[cousin]"
    And I press "Save"
    Then a new email is sent:
      | to   | subject           | body   | body  |
      | Fred | Unassigned: Child | cousin | child |
    Given I am logged in as "Fred Bloggs"
    When I follow the link to "/discuss/" from the email to Fred
    Then I am visiting "/discuss/cousin/child"
    And I should see "Some body text here"
