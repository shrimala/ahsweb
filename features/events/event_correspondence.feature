@api
Feature: Events Corresponding Entity References
  In order to set sessions from an event and vice versa
  As any user editing an event or a session
  I need my changes to be synced from the event to the sessions, or from the session to the event

  Background:
    Given I am logged in as an administrator

  Scenario: Creating an event with a session sets the event reference on the session
    Given a session with the title "Session1"
    And "event" content:
      | title      | field_sessions   |
      | Event1     | Session1         |
    When I am at "/admin/content"
    And I click "Event1"
    And I click "Manage recordings"
    Then I should see "Sessions in an IEF complex widget":
      | contains  |
      | Session1  |
    When I click "Session1"
    Then I should see "Event1" displayed from the "field_event" field

  Scenario: Creating an event with 2 sessions sets the event reference on both sessions
    Given a session with the title "Session1"
    And a session with the title "Session2"
    And "event" content:
      | title      | field_sessions     |
      | Event1     | Session1, Session2 |
    When I am at "/admin/content"
    And I click "Event1"
    And I click "Manage recordings"
    Then I should see "Sessions in an IEF complex widget":
      | contains  |
      | Session1  |
      | Session2  |
    When I click "Session1"
    Then I should see "Event1" displayed from the "field_event" field
    When I am at "/admin/content"
    And I click "Event1"
    And I click "Manage recordings"
    And I click "Session2"
    Then I should see "Event1" displayed from the "field_event" field

  @skip
  # Don't need this, there shouldn't be a UI to add an existing session as a reference from an event
  Scenario: An event cannot reference a session that is already referenced by another event
    Given a session with the title "Session1"
    Given "event" content:
      | title      | field_sessions |
      | Event1     | Session1       |
      | Event2     |                |
    When I am at "/admin/content"
    And I click "Event2"
    Then I should not see "Event1" in the list!!!

  Scenario: Removing a session from an event removes the event reference on the session
    Given a session with the title "Session1"
    And "event" content:
      | title      | field_sessions |
      | Event1     | Session1       |
    When I am at "/admin/content"
    And I click "Event1"
    And I click "Manage recordings"
    Then I should see "Sessions in an IEF complex widget":
      | contains  |
      | Session1  |
    And I press the "Remove" button in the "Session1" row
    And I press the "Remove" button in the "Are you sure you want to remove Session1?" row
    And I press the "Save" button
    Then I should see no "Sessions in an IEF complex widget" elements
    When I am at "/admin/content"
    And I click "Session1"
    Then I should not see "Event1" displayed from the "field_event" field

  Scenario: Manually creating a session from an event
    Given session_type terms:
      | name       |
      | Meditation |
      | Teaching   |
    And an event with the title "Event1"
    When I am at "/admin/content"
    And I click "Event1"
    And I click "Manage recordings"
    And I press the "Add new session" button
    And I fill in "field_sessions[form][inline_entity_form][title][0][value]" with "Session1"
    And I select "Meditation" from "field_sessions[form][inline_entity_form][field_session_type]"
    And I press the "Create session" button
    And I press the "Save and keep published" button
    When I am at "/admin/content"
    Then I should see "Session1"
    When I click "Session1"
    Then I should see "Event1" displayed from the "field_event" field

  Scenario: Manually referencing an event from a session
    Given session_type terms:
      | name       |
      | Meditation |
    And an event with the title "Event1"
    Given session content:
      | title      | field_session_type |
      | Session1   | Meditation         |
    When I am at "/admin/content"
    When I click "Edit" in the "Session1" row
    When I fill in "field_event[0][target_id]" with "Event1"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "Event1"
    And I click "Manage recordings"
    Then I should see "Session1" displayed from the "field_sessions" field

  Scenario: Changing the event on a session adds a session reference on the event
    Given session_type terms:
      | name       |
      | Meditation |
    Given event content:
      | title      |
      | Event1     |
      | Event2     |
    Given session content:
      | title      | field_event | field_session_type |
      | Session1   | Event1      | Meditation         |
    When I am at "/admin/content"
    When I click "Edit" in the "Session1" row
    When I fill in "field_event[0][target_id]" with "Event2"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "Session1"
    Then I should not see "Event1" displayed from the "field_event" field
    And I should see "Event2" displayed from the "field_event" field
    When I am at "/admin/content"
    And I click "Event1"
    And I click "Manage recordings"
    Then I should see no "Sessions in an IEF complex widget" elements
    When I am at "/admin/content"
    And I click "Event2"
    And I click "Manage recordings"
    Then I should see "Sessions in an IEF complex widget":
      | contains  |
      | Session1  |

  Scenario: Cannot autocreate an event reference from a session
    Given a session with the title "Session1"
    When I am at "/admin/content"
    And I click "Edit" in the "Session1" row
    And I fill in "field_event[0][target_id]" with "Autocreated_should_not_exist"
    And I press the "Save" button
    Then I should see "There are no entities matching"
    When I am at "/admin/content"
    Then I should not see "Autocreated_should_not_exist"
