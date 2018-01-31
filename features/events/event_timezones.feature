@api @skip
Feature: Event time zones
  In order to understand times of events
  As any user with access to events
  I need to see event times in the venue's time zone

  Background:
    # Pacific/Midway (UTC -11), Africa/Nairobi (UTC + 3), Asia/Kolkata (UTC + 05:30)
    # and America/Phoenix (UTC -7) never have DST.
    Given users:
      | name          | timezone          | status | roles         |
      | Fred Midway   | Pacific/Midway    | 1      | Administrator |
      | Jane Tokyo    | Asia/Tokyo        | 1      |               |
    Given venue content:
      | title                     | field_timezone  |
      | A venue in Phoenix        | America/Phoenix |
      | A venue in Kolkata        | Asia/Kolkata    |
      | A venue without time zone |                 |
    # Dates are created in site default time Europe/London
    # but reinterpreted to venue time presave.
    Given event content:
      | title           | field_datetime      | field_venue               |
      | Event1          | 2035-12-29 12:00:00 | A venue in Phoenix        |
      | Event2          | 2035-12-29 12:00:00 |                           |
      | Event3          | 2035-12-29 12:00:00 | A venue without time zone |

  Scenario: Event start shown according to venue time zone
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Editing event does not change start time unexpectedly
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    When I click "Edit event"
    And I fill in "title[0][value]" with "Event4"
    And I press "Save and keep published"
    And I visit "events/2035/12/29/event4"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/event4"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Event start shown in default time zone if no venue
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event2"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/event2"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Event start shown in default time zone if no venue time zone
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event3"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/event3"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Changing venue time zone keeps event start time
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    When I visit "admin/content"
    And I click "Edit" in the "A venue in Phoenix" row
    And I select "Asia/Kolkata" from "field_timezone[0][value]"
    And I press "Save and keep published"
    And I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Changing venue keeps event start time
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    When I click "Edit event"
    And I fill in "field_venue[0][target_id]" with "A venue in Kolkata"
    And I press "Save and keep published"
    And I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Changing venue from one without time zone keeps event start time
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event3"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    When I click "Edit event"
    And I fill in "field_venue[0][target_id]" with "A venue in Kolkata"
    And I press "Save and keep published"
    And I visit "events/2035/12/29/event3"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/event3"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Changing venue from none keeps event start time
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event2"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    When I click "Edit event"
    And I fill in "field_venue[0][target_id]" with "A venue in Kolkata"
    And I press "Save and keep published"
    And I visit "events/2035/12/29/event2"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/event2"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Removing venue keeps event start time
    Given I am logged in as "Fred Midway"
    When I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    When I click "Edit event"
    And I empty the field "field_venue[0][target_id]"
    And I press "Save and keep published"
    And I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/event1"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Creating an event without venue uses default venue time zone
    Given I am logged in as "Fred Midway"
    When I visit "node/add/event"
    And I fill in "title[0][value]" with "TestEvent"
    And I fill in "field_datetime[0][value][date]" with "2035-12-29"
    And I fill in "field_datetime[0][value][time]" with "12:00"
    And I press "Save and publish"
    And I visit "events/2035/12/29/testevent"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/testevent"
    Then I should see "Saturday 29 December 2035, 12:00pm"

  Scenario: Creating an event with venue uses venue time zone
    Given I am logged in as "Fred Midway"
    When I visit "node/add/event"
    And I fill in "title[0][value]" with "TestEvent"
    And I fill in "field_datetime[0][value][date]" with "2035-12-29"
    And I fill in "field_datetime[0][value][time]" with "12:00"
    And I fill in "field_venue[0][target_id]" with "A venue in Kolkata"
    And I press "Save and publish"
    And I visit "events/2035/12/29/testevent"
    Then I should see "Saturday 29 December 2035, 12:00pm"
    Given I am logged in as "Jane Tokyo"
    When I visit "events/2035/12/29/testevent"
    Then I should see "Saturday 29 December 2035, 12:00pm"