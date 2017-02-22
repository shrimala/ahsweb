@api
Feature: Changes to parents/children are reflected in their changes comments
  In order to track history of discussions
  As any user allowed to edit a discussion
  I need to have my participation recorded even if indirect.

  Background:
    Given users:
      | name         | status |
      | Fred Bloggs  | 1      |
      | Jane Doe     | 1      |

  Scenario: Adding a child makes me a participant in the child, and a change record
    Given a "discussion" with the title "Parent"
    And a "discussion" by "Fred Bloggs" with the title "Child"
    And I am logged in as "Jane Doe"
    When I visit "/discuss/parent"
    And I fill in "field_children[0][target_id]" with "[Child]"
    And I press "Save"
    And I visit "/discuss/parent/child"
    Then the "field_participants[]" select field should have "Fred Bloggs, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |

  Scenario: Adding a parent makes me a participant in the parent, and a change record
    Given a "discussion" with the title "Child"
    And a "discussion" by "Fred Bloggs" with the title "Parent"
    And I am logged in as "Jane Doe"
    When I visit "/discuss/child"
    And I fill in "field_parents[0][target_id]" with "Parent"
    And I press "Save"
    And I visit "/discuss/parent"
    Then the "field_participants[]" select field should have "Fred Bloggs, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |

  Scenario: Creator is automatically participant but not assigned when autocreating
    Given I am logged in as "Fred Bloggs"
    And I am viewing a "discussion" by me with the title "Parent"
    When I fill in "field_children[0][target_id]" with "Child"
    And I press "Save"
    And I visit "/discuss/parent/child"
    Then the "field_participants[]" select field should have "Fred Bloggs" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
