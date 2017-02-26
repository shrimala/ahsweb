@api
Feature: Showing changes to discussion fields
  In order to understand how a discussion has been modified
  As a user viewing a discussion
  I need to see a record of changes to fields.

  Background:
    Given users:
      | name         | status |
      | Fred Bloggs  | 1      |
      | Jane Doe     | 1      |
      | John Doe     | 1      |
      | Hans Schmidt | 1      |

  Scenario: Changes to a discussion show up in detail in the comments
    Given I am logged in as "Fred Bloggs"
    And a "discussion" by me with the title "Parent"
    And a "discussion" by me with the title "Test1"
    When I fill in "title[0][value]" with "Test2"
    And I select "Jane Doe" from "field_assigned[]"
    And I additionally select "John Doe" from "field_assigned[]"
    And I additionally select "Jane Doe" from "field_participants[]"
    And I additionally select "John Doe" from "field_participants[]"
    And I additionally select "Hans Schmidt" from "field_participants[]"
    And I check "Help wanted"
    And I check "Finished"
    And I fill in "body[0][value]" with "Some body value"
    And I fill in "field_children[0][target_id]" with "Child1"
    And I fill in "field_parents[0][target_id]" with "Parent"
    And I press "Save"
    Then I should see "Change records" in the "article.comment:nth-of-type(2)" element:
      | :contains             |
      | Changed the title     |
      | Marked as finished    |
      | Marked as help wanted |
      | Added participants Hans Schmidt, Jane Doe and John Doe |
      | Changed the summary (show changes) |
      | Assigned as a task to Jane Doe and John Doe |
      | Added as part of this 'Child1'  |
      | Marked this as part of 'Parent' |
    When I comment "Some comment"
    And I select "Jane Doe" from "field_assigned[]"
    And I select "Fred Bloggs" from "field_participants[]"
    And I uncheck "Finished"
    And I empty the field "field_parents[0][target_id]"
    And I press "Save"
    Then I should see "Change records" in the "article.comment:nth-of-type(4)" element:
      | :contains             |
      | Unassigned as a task from John Doe  |
      | Marked as not finished    |
      | Marked this as not part of 'Parent' |
      | Removed participants Hans Schmidt, Jane Doe and John Doe |

  Scenario: When a user makes an edit of a field, his addition to field_participants is omitted from diff
    Given I am logged in as "Fred Bloggs"
    And a "discussion" by "Jane Doe" with the title "Test1"
    When I fill in "title[0][value]" with "Test2"
    And I press "Save"
    Then I should see "Change records" in the "article.comment:nth-of-type(2)" element:
      | :contains             |
      | Changed the title     |

  Scenario: When a user makes an edit of fields includling adding a partipant, his addition to field_participants is omitted from diff
    Given I am logged in as "Fred Bloggs"
    And a "discussion" by "Jane Doe" with the title "Test1"
    When I fill in "title[0][value]" with "Test2"
    And I additionally select "John Doe" from "field_participants[]"
    And I press "Save"
    Then I should see "Change records" in the "article.comment:nth-of-type(2)" element:
      | :contains                  |
      | Changed the title          |
      | Added participant John Doe |

  Scenario: When a user makes an edit of fields including removing a partipant, his addition to field_participants is omitted from diff
    Given I am logged in as "Fred Bloggs"
    And a "discussion" by "Jane Doe" with the title "Test1"
    When I fill in "title[0][value]" with "Test2"
    And I select "Fred Bloggs" from "field_participants[]"
    And I press "Save"
    Then I should see "Change records" in the "article.comment:nth-of-type(2)" element:
      | :contains                    |
      | Changed the title            |
      | Removed participant Jane Doe |