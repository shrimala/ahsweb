@api @debug
Feature: Mark which users as participants
  In order to highlight discussions to the right users
  As any user allowed to edit a discussion
  I need to see and edit a list of participants.

  Background:
    Given users:
      | name         | status |
      | Fred Bloggs  | 1      |
      | Jane Doe     | 1      |
      | Hans Schmidt | 1      |

  Scenario: Creator is automatically participant but not assigned when Behat creating
    Given I am logged in as "Fred Bloggs"
    And I am viewing a "discussion" by me with the title "Test"
    Then the "field_participants[]" select field should have "Fred Bloggs" selected
    And the "field_assigned[]" select field should have nothing selected

  Scenario: Creator is automatically participant but not assigned when using add form
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/add"
    And I fill in "title[0][value]" with "Test"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs" selected
    And the "field_assigned[]" select field should have nothing selected

  Scenario: Commenter is added as participant
    Given a "discussion" by "Fred Bloggs" with the title "Test"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I comment "Some comment"
    Then the "field_participants[]" select field should have "Fred Bloggs, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected

  Scenario: Editor is added as participant
    Given a "discussion" by "Fred Bloggs" with the title "Test"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I fill in "title[0][value]" with "Test2"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected

  Scenario: Editor & commenter is added as participant
    Given a "discussion" by "Fred Bloggs" with the title "Test"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I fill in "title[0][value]" with "Test2"
    And I fill in "comment[value]" with "Some comment"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected

  Scenario: Simply add oneself as participant
    Given a "discussion" by "Fred Bloggs" with the title "Test"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I additionally select "Jane Doe" from "field_participants[]"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | Yes           |
      |                          | Yes           |

  Scenario: Simply add oneself as participant while commenting
    Given a "discussion" by "Fred Bloggs" with the title "Test"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I additionally select "Jane Doe" from "field_participants[]"
    And I fill in "comment[value]" with "Some comment"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | Yes           |
      |                          | Yes           |
      | Some comment             | No            |

  Scenario: Simply add oneself as participant while editing
    Given a "discussion" by "Fred Bloggs" with the title "Test"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I additionally select "Jane Doe" from "field_participants[]"
    And I fill in "title[0][value]" with "Test2"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | Yes           |
      |                          | Yes           |

  Scenario: Add someone else as participant
    Given a "discussion" by "Fred Bloggs" with the title "Test"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I additionally select "Hans Schmidt" from "field_participants[]"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs, Hans Schmidt, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | Yes           |
      |                          | Yes           |

  Scenario: Add someone else as participant while commenting
    Given a "discussion" by "Fred Bloggs" with the title "Test"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I additionally select "Hans Schmidt" from "field_participants[]"
    And I fill in "comment[value]" with "Some comment"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs, Hans Schmidt, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | Yes           |
      |                          | Yes           |
      | Some comment             | No            |

  Scenario: Add someone else as participant while editing
    Given a "discussion" by "Fred Bloggs" with the title "Test"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    And I additionally select "Hans Schmidt" from "field_participants[]"
    And I fill in "title[0][value]" with "Test2"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs, Hans Schmidt, Jane Doe" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | Yes           |
      |                          | Yes           |

  Scenario: If already a participant, editing or commenting does not remove
    Given I am logged in as "Fred Bloggs"
    And I am viewing a "discussion" by me with the title "Test"
    Then the "field_participants[]" select field should have "Fred Bloggs" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | No            |
    When I comment "Some comment 1"
    Then the "field_participants[]" select field should have "Fred Bloggs" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | No            |
      | Some comment 1           | No            |
    When I fill in "title[0][value]" with "Test2"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | Yes           |
      | Some comment 1           | No            |
      |                          | Yes           |
    When I comment "Some comment 2"
    And I fill in "title[0][value]" with "Test3"
    And I fill in "comment[value]" with "Some comment 3"
    And I press "Save"
    Then the "field_participants[]" select field should have "Fred Bloggs" selected
    And the "field_assigned[]" select field should have nothing selected
    And I should see "Discussion comments":
      | Comment body field       | Changes field |
      | Started this discussion. | Yes           |
      | Some comment 1           | No            |
      |                          | Yes           |
      | Some comment 2           | No            |
      |                          | Yes           |
      | Some comment 3           | No            |

  Scenario: Participant can remove himself
    Given "discussion" content:
      | title | field_participants    |
      | Test  | Fred Bloggs, Jane Doe |
    And I am logged in as "Fred Bloggs"
    When I visit "discuss/test"
    Then the "field_participants[]" select field should have "Fred Bloggs, Jane Doe" selected
    When I select "Jane Doe" from "field_participants[]"
    And I press "Save"
    Then the "field_participants[]" select field should have "Jane Doe" selected
