@api
Feature: Event sessions
  In order to participate in an event live
  As any user with access to event sessions
  I need to see coming event sessions according to both venue and my time zones.

  Background:
    # Pacific/Midway (UTC -11), Africa/Nairobi (UTC + 3), Asia/Kolkata (UTC + 05:30)
    # and America/Phoenix (UTC -7) never have DST.
    # So if a session is at 12pm for an event in Phoenix, that will be 00:30  the next
    # day for a user in Kolkata.
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

  Scenario: Sessions in coming and past sections use venue time zone
  Given "youtube" "media" entities:
    | name    | field_media_video_embed_field               |
    | Video1  | https://www.youtube.com/watch?v=9bZkp7q19f0 |
    Given session content:
      | title           | field_datetime      | field_event | field_session_type | field_media |
      | Session4        | 2035-12-30 12:00:00 | Event1      | Teaching           |             |
      | Session5        | 2035-12-31 12:00:00 | Event1      | Teaching           |             |
      | Session6        | 2005-12-30 12:00:00 | Event1      | Teaching           | Video1      |
  And I am logged in as "Fred Midway"
  When I visit "events/2035/12/29/event1"
  And I click "Online"
  Then I should see "Coming session day groupings":
    | contains           |
    | Sunday 30 December |
    | Monday 31 December |
  And I should see "Coming sessions":
      | Title field | Datetime field |
      | Session4    | 12:00pm        |
      | Session5    | 12:00pm        |
  And I should see "Finished session headings":
      | Title field | Datetime field              |
      | Session6    | Friday 30 December, 12:00pm |
  Given I am logged in as "Jane Kolkata"
  When I visit "events/2035/12/29/event1"
  And I click "Online"
  Then I should see "Coming session day groupings":
    | contains           |
    | Sunday 30 December |
    | Monday 31 December |
  And I should see "Coming sessions":
    | Title field | Datetime field |
    | Session4    | 12:00pm        |
    | Session5    | 12:00pm        |
  And I should see "Finished session headings":
    | Title field | Datetime field              |
    | Session6    | Friday 30 December, 12:00pm |

  Scenario: Viewing and editing an individual session directly
    Given I am logged in as "Fred Midway"
    And session content:
      | title           | field_datetime      | field_event | field_session_type |
      | Session4        | 2035-12-28 12:00:00 | Event1      | Teaching           |
    When I visit "events/2035/12/29/event1"
    And I click "Session4"
    Then I should see "Friday 28 December 2035, 12:00pm" in the "Datetime field"
    # Editing the session does not mess with the date
    When I click "Edit"
    And I fill in "title[0][value]" with "Session5"
    And I press "Save and keep published"
    Then I should see "Friday 28 December 2035, 12:00pm" in the "Datetime field"
    When I click "Edit"
    # Deliberate change to the date save OK using venue time zone.
    And I fill in "field_datetime[0][value][time]" with "10:00"
    And I press "Save and keep published"
    Then I should see "Friday 28 December 2035, 10:00am" in the "Datetime field"
    When I visit "events/2035/12/29/event1"
    And I click "Online"
    Then I should see "Next session":
      | Title field | Datetime field                |
      | Session5    | Friday 28 December, 10:00am(Friday 6:00am in your time Pacific/Midway) |
