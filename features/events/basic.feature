@api
Feature: Events listings
  In order to offer events and give context for teaching sessions
    we need a record of events 

  # Events are accessible to anonymous users
  Scenario: Events have URL events/year/month/day/titleword1-titleword2-etc
    Given "event" "group" entities:
      | label         | field_dates                               | field_description |
      | event1        | 2005-12-30 00:00:00 - 2005-12-30 00:00:00 | test description  |
      | event2        | 2005-10-01 11:05:23 - 2005-11-01 11:05:23 | test description  |
      | Event 3       | 2005-10-01 13:00:00 - 2005-10-01 15:00:00 | test description  |
      | Event, and 4  | 2005-10-01 13:00:00 - 2005-10-01 13:00:00 | test description  |
    When I visit "events/2005/12/30/event1"
    Then I should see "test description"
    When I visit "events/2005/10/01/event2"
    Then I should see "test description"
    When I visit "events/2005/10/01/event-3"
    Then I should see "test description"
    When I visit "events/2005/10/01/event-and-4"
    Then I should see "test description"

  @skip
  Scenario: Events url use venue time zone
    # Event times are interpreted in site default time (Europe/London)
    # by Behat, but then reinterpreted using venue time zone at presave.
    # No venue = use default venue timezone Europe/London = BST on 30 June.
    # So paths use the venue time zone date, not the UTC date.
    Given venue content:
      | title         | field_timezone  |
      | Venue1        | America/Phoenix |
      | Venue2        | Asia/Kolkata    |
    Given event content:
      | title         | field_datetime      | field_venue |
      | event1        | 2005-12-30 03:00:00 | Venue1      |
      | event2        | 2005-12-30 23:00:00 | Venue2      |
      | event3        | 2005-06-30 23:30:00 |             |
    When I visit "events/2005/12/30/event1"
    And I visit "events/2005/12/30/event2"
    And I visit "events/2005/06/30/event3"
    Then the response status code should be 200

  #Sanitisation is handled by the ahs_miscellaneous module.
  Scenario: Event title is capitalised and sanitised
    Given I am logged in as an "administrator"
    When I visit "/group/add/event"
    And I fill in "label[0][value]" with " test / Title something"
    And I fill in "field_dates[0][value][date]" with "2035-10-01"
    And I fill in "field_dates[0][value][time]" with "16:00"
    And I fill in "field_dates[0][end_value][date]" with "2035-10-01"
    And I fill in "field_dates[0][end_value][time]" with "16:00"
    And I press the "Create Event" button
    Then I am visiting "/events/2035/10/01/test-title-something"
    And I should see exactly "Test Title something"
    When I visit "/group/add/event"
    And I fill in "label[0][value]" with " test / Title something"
    And I fill in "field_dates[0][value][date]" with "2035-10-02"
    And I fill in "field_dates[0][value][time]" with "16:00"
    And I fill in "field_dates[0][end_value][date]" with "2035-10-02"
    And I fill in "field_dates[0][end_value][time]" with "16:00"
    And I press the "Create Event" button
    Then I am visiting "/events/2035/10/02/test-title-something"
    And I should see exactly "Test Title something"
    When I visit "/group/add/event"
    And I fill in "label[0][value]" with "TEST"
    And I fill in "field_dates[0][value][date]" with "2035-10-03"
    And I fill in "field_dates[0][value][time]" with "16:00"
    And I fill in "field_dates[0][end_value][date]" with "2035-10-03"
    And I fill in "field_dates[0][end_value][time]" with "16:00"
    And I press the "Create Event" button
    Then I am visiting "/events/2035/10/03/test"
    And I should see exactly "TEST"
    When I visit "/group/add/event"
    And I fill in "label[0][value]" with "TEST SOMETHING"
    And I fill in "field_dates[0][value][date]" with "2035-10-04"
    And I fill in "field_dates[0][value][time]" with "16:00"
    And I fill in "field_dates[0][end_value][date]" with "2035-10-04"
    And I fill in "field_dates[0][end_value][time]" with "16:00"
    And I press the "Create Event" button
    Then I am visiting "/events/2035/10/04/test-something"
    And I should see exactly "Test something"