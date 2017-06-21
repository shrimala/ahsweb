@api
Feature: Event sessions
  In order to create and edit sessions for events around the world
  As any user anywhere in the world who can create or edit sessions
  I need to be able to set session times assuming the venue's time zone.

  Background:
    # Pacific/Midway (UTC -11), Africa/Nairobi (UTC + 3), Asia/Kolkata (UTC + 05:30)
    # and America/Phoenix (UTC -7) never have DST.
    Given users:
      | name          | timezone          | status | roles         |
      | Fred Midway   | Pacific/Midway    | 1      | Administrator |
      | Jane Kolkata  | Asia/Kolkata      | 1      |               |
      | John London   | Europe/London     | 1      |               |
      | Harry Phoenix | America/Phoenix   | 1      |               |
    Given venue content:
      | title                     | field_timezone  |
      | A venue in Phoenix        | America/Phoenix |
      | A venue in Nairobi        | Africa/Nairobi  |
      | A venue without time zone |                 |
    Given event content:
      | title           | field_datetime      | field_venue               |
      | Event1          | 2035-12-29 12:00:00 | A venue in Phoenix        |
      | Event2          | 2035-12-29 12:00:00 |                           |
      | Event3          | 2035-12-29 12:00:00 | A venue without time zone |
    And session_type terms:
      | name       |
      | Teaching   |
      | Meditation |
    Given I am logged in as "Fred Midway"
    # Create a session for each event
    When I visit "events/2035/12/29/event1"
    And I click "Manage online"
    And I press the "field_sessions_add_more" button
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][date]" with "2035-12-29"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][time]" with "12:00"
    And I fill in "field_sessions[0][inline_entity_form][title][0][value]" with "Session1"
    And I select "Teaching" from "field_sessions[0][inline_entity_form][field_session_type]"
    And I press the "Save and keep published" button
    When I visit "events/2035/12/29/event2"
    And I click "Manage online"
    And I press the "field_sessions_add_more" button
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][date]" with "2035-12-29"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][time]" with "12:00"
    And I fill in "field_sessions[0][inline_entity_form][title][0][value]" with "Session2"
    And I select "Teaching" from "field_sessions[0][inline_entity_form][field_session_type]"
    And I press the "Save and keep published" button
    When I visit "events/2035/12/29/event3"
    And I click "Manage online"
    And I press the "field_sessions_add_more" button
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][date]" with "2035-12-29"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][time]" with "12:00"
    And I fill in "field_sessions[0][inline_entity_form][title][0][value]" with "Session3"
    And I select "Teaching" from "field_sessions[0][inline_entity_form][field_session_type]"
    And I press the "Save and keep published" button

  Scenario: Event sessions list shows local time as well as venue time
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session1    | Saturday 29 December, 12:00pm(Saturday 8:00am in your time Pacific/Midway) |
    Given I am logged in as "Jane Kolkata"
    When I visit "events/2035/12/29/event1"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field               |
      | Session1    | Saturday 29 December, 12:00pm(Sunday 12:30am in your time Asia/Kolkata)|

  Scenario: Event sessions list for events without venue
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event2"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session2    | Saturday 29 December, 12:00pm(Saturday 1:00am in your time Pacific/Midway)|
    Given I am logged in as "Jane Kolkata"
    When I visit "events/2035/12/29/event2"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field               |
      | Session2    | Saturday 29 December, 12:00pm(Saturday 5:30pm in your time Asia/Kolkata)|

  Scenario: Event sessions list events with venue without time zone
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event3"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session3    | Saturday 29 December, 12:00pm(Saturday 1:00am in your time Pacific/Midway)|
    Given I am logged in as "Jane Kolkata"
    When I visit "events/2035/12/29/event3"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field               |
      | Session3    | Saturday 29 December, 12:00pm(Saturday 5:30pm in your time Asia/Kolkata)|

  Scenario: Sessions edited using 'Manage online' use venue time zone
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    And I click "Manage online"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][date]" with "2035-12-28"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][time]" with "15:00"
    And I press the "Save and keep published" button
    And I click "Online"
    # time displayed in same timezone as entered. Event1 is in America/Phoenix.
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session1    | Friday 28 December, 3:00pm(Friday 11:00am in your time Pacific/Midway) |
    When I visit "events/2035/12/29/event2"
    And I click "Manage online"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][date]" with "2035-12-28"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][time]" with "15:00"
    And I press the "Save and keep published" button
    And I click "Online"
    # time displayed in same timezone as entered. Event2 has no venue, assumes Europe/London.
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session2    | Friday 28 December, 3:00pm(Friday 4:00am in your time Pacific/Midway) |
    When I visit "events/2035/12/29/event3"
    And I click "Manage online"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][date]" with "2035-12-28"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][time]" with "15:00"
    And I press the "Save and keep published" button
    And I click "Online"
    # time displayed in same timezone as entered. Event3 venue has not timezone, assumes Europe/London.
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session3    | Friday 28 December, 3:00pm(Friday 4:00am in your time Pacific/Midway) |

  Scenario: Sessions edited using 'Manage recordings' use venue time zone
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    And I click "Manage recordings"
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Sat 29 Dec, 12:00pm |
    When I press the "Edit" button in the "Session1" row
    And I fill in "field_sessions[form][inline_entity_form][entities][0][form][field_datetime][0][value][time]" with "10:00"
    And I press the "Update session" button
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Sat 29 Dec, 10:00am |
    When I press the "Save and keep published" button
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session1    | Saturday 29 December, 10:00am(Saturday 6:00am in your time Pacific/Midway) |

  Scenario: Sessions edited using 'Manage recordings' use default venue time zone when no venue
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event2"
    And I click "Manage recordings"
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session2    | Sat 29 Dec, 12:00pm |
    When I press the "Edit" button in the "Session2" row
    And I fill in "field_sessions[form][inline_entity_form][entities][0][form][field_datetime][0][value][time]" with "10:00"
    And I press the "Update session" button
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session2    | Sat 29 Dec, 10:00am |
    When I press the "Save and keep published" button
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session2    | Saturday 29 December, 10:00am(Friday 11:00pm in your time Pacific/Midway) |

  Scenario: Sessions added using 'Manage recordings' use venue time zone
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    And I click "Manage recordings"
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Sat 29 Dec, 12:00pm |
    And I press the "Add new session" button
    And I fill in "field_sessions[form][inline_entity_form][title][0][value]" with "New session"
    And I fill in "field_sessions[form][inline_entity_form][field_datetime][0][value][date]" with "2035-12-29"
    And I fill in "field_sessions[form][inline_entity_form][field_datetime][0][value][time]" with "10:00"
    And I select "Meditation" from "field_sessions[form][inline_entity_form][field_session_type]"
    And I press the "Create session" button
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field       |
      | Session1    | Sat 29 Dec, 12:00pm  |
      | New session | Sat 29 Dec, 10:00am  |
    When I press the "Save and keep published" button
    And I click "Manage recordings"
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field       |
      | Session1    | Sat 29 Dec, 12:00pm  |
      | New session | Sat 29 Dec, 10:00am  |
    When I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field                |
      | New session | Saturday 29 December, 10:00am(Saturday 6:00am in your time Pacific/Midway) |


  # chck directly editing the session
