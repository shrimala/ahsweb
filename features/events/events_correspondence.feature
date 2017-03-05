@api
Feature: Events Corresponding Entity References
  In order to set sessions from an event and vice versa
  As any user editing an event or a session
  I need my changes to be synced from the event to the sessions, or from the session to the event

  #These tests assume normal entity reference widget behaviors.

  Background:
    Given I am logged in as an administrator

  Scenario: Creating an event with a session sets the event reference on the session
    Given a session with the title "Session1"
    Given "event" content:
      | title      | field_sessions   |
      | Event1     | Session1         |
    When I am at "/admin/content"
    And I click "Event1"
    Then I should see "Session1" displayed from the "field_sessions" field
    When I click "Session1"
    Then I should see "Event1" displayed from the "field_event" field

  Scenario: Creating an event with 2 sessions sets the event reference on both sessions
    Given a session with the title "Session1"
    Given a session with the title "Session2"
    Given "event" content:
      | title      | field_sessions     |
      | Event1     | Session1, Session2 |
    When I am at "/admin/content"
    When I click "Event1"
    Then I should see "Session1" displayed from the "field_sessions" field
    When I click "Session1"
    Then I should see "Event1" displayed from the "field_event" field
    When I am at "/admin/content"
    When I click "Event1"
    Then I should see "Session2" displayed from the "field_sessions" field
    When I click "Session2"
    Then I should see "Event1" displayed from the "field_event" field

  @skip
  Scenario: An event cannot reference a session that is already referenced by another event
    Given a session with the title "Session1"
    Given "event" content:
      | title      | field_sessions |
      | Event1     | Session1       |
      | Event2     |                |
    When I am at "/admin/content"
    When I click "Event2"
    Then I should not see "Event1" in the list!!!

  Scenario: Removing a session from an event removes the event reference on the session
    Given a session with the title "Session1"
    Given "event" content:
      | title      | field_sessions |
      | Event1     | Session1       |
    When I am at "/admin/content"
    When I click "Edit" in the "Event1" row
    And I empty the field "field_sessions[0][target_id]"
    And I press the "Save" button
    Then I should not see "Session1" displayed from the "field_sessions" field
    When I am at "/admin/content"
    And I click "Session1"
    Then I should not see "Event1" displayed from the "field_event" field
  @skip
  Scenario: Updating an event with a new session reference sets the event reference on the session
    Given a session content with the title "Session1"
    Given a session content with the title "Session2"
    Given event content:
      | title       | field_sessions |
      | Event1     | Session1         |
    When I am at "/admin/content"
    And I click "Edit" in the "Event1" row
    And I fill in "field_sessions[0][target_id]" with "[Session2]"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "Event1"
    Then I should not see "Session1" displayed from the "field_sessions" field
    And I should see "Session2" displayed from the "field_sessions" field
    When I am at "/admin/content"
    And I click "Session1"
    Then I should not see "Event1" displayed from the "field_event" field
    When I am at "/admin/content"
    And I click "Session2"
    Then I should see "Event1" displayed from the "field_event" field

  Scenario: Changing the event on a session adds a session reference on the event
    Given event content:
      | title       |
      | Event1     |
      | Event2     |
    Given session content:
      | title      | field_event   |
      | Session1     | Event1         |
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
    Then I should not see "Session1" displayed from the "field_sessions" field
    When I am at "/admin/content"
    And I click "Event2"
    Then I should see "Session1" displayed from the "field_sessions" field
  @test
  Scenario: Manually referencing an event from a session
    Given an event with the title "Event1"
    Given a session with the title "Session1"
    When I am at "/admin/content"
    When I click "Edit" in the "Session1" row
    When I fill in "field_event[0][target_id]" with "Event1"
    And I press the "Save" button
    When I am at "/admin/content"
    And I click "Event1"
    Then I should see "Session1" displayed from the "field_sessions" field

  @skip @hasDrupalError
  Scenario: Cannot autocreate an event reference from a session
    Given a discussion content with the title "Session1"
    When I am at "/admin/content"
    And I click "Session1"
    And I fill in "field_event[0][target_id]" with "Autocreated_should_not_exist"
    And I press the "Save" button
    Then I should see the error message "There are no entities matching"
    When I am at "/admin/content"
    Then I should not see "Autocreated_should_not_exist"
