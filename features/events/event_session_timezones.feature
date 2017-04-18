@api
Feature: Event sessions
  In order to participate in an event live
  As any user with access to event sessions
  I need to see coming event sessions according to both venue and my time zones.

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
    And "Session type" terms:
      | name       |
      | Teaching   |
    And session content:
      | title           | field_datetime      | field_event | field_session_type |
      | Session1        | 2035-12-29 12:00:00 | Event1      | Teaching           |
      | Session2        | 2035-12-29 12:00:00 | Event2      | Teaching           |
      | Session3        | 2035-12-29 12:00:00 | Event3      | Teaching           |

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

  Scenario: Event sessions list shows only venue time for local person
    Given I am logged in as "Harry Phoenix"
    When I visit "events/2035/12/29/event1"
    And I click "Online"
    # I'm not sure this does an exact match, so may not be effective in testing that
    #time zone is not shown
    Then I should see "Next session":
      | Title field  | Datetime field                |
      | Session1     | Saturday 29 December, 12:00pm |
    And I should not see "in your time America/Phoenix"

  Scenario: Event sessions list for events without venue
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event2"
    And I click "Online"
    Then I should see "Next session":
      | Title field  | Datetime field                |
      | Session2     | Saturday 29 December, 12:00pm(Saturday 1:00am in your time Pacific/Midway)|
    Given I am logged in as "Jane Kolkata"
    When I visit "events/2035/12/29/event2"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field               |
      | Session2    | Saturday 29 December, 12:00pm(Saturday 5:30pm in your time Asia/Kolkata)|
    Given I am logged in as "John London"
    When I visit "events/2035/12/29/event2"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session2    | Saturday 29 December, 12:00pm |
    And I should not see "in your time Europe/London"

  Scenario: Event sessions list events with venue without time zone
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event3"
    And I click "Online"
    Then I should see "Next session":
      | Title field  | Datetime field                |
      | Session3     | Saturday 29 December, 12:00pm(Saturday 1:00am in your time Pacific/Midway)|
    Given I am logged in as "Jane Kolkata"
    When I visit "events/2035/12/29/event3"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field               |
      | Session3    | Saturday 29 December, 12:00pm(Saturday 5:30pm in your time Asia/Kolkata)|
    Given I am logged in as "John London"
    When I visit "events/2035/12/29/event3"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session3    | Saturday 29 December, 12:00pm |
    And I should not see "in your time Europe/London"

  Scenario: Sessions on events 'Manage recordings' use venue time zone
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    And I click "Manage recordings"
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Sat 29 Dec, 12:00pm |
