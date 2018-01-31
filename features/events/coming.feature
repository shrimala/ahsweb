@api
Feature: Coming events
  In order to know what the Sangha has to offer
  As a visitor
  I need to see a list of coming events

  Scenario: Events are listed closest first
    Given "event" "group" entities:
      | label         | field_dates                               | field_published | field_private | field_description   |
      | event1        | 2035-12-30 00:00:00 - 2005-12-30 00:00:00 | 1               | 0             | test description 1  |
      | event2        | 2035-10-02 11:05:23 - 2005-11-01 11:05:23 | 1               | 0             | test description 2  |
      | event6        | 2035-10-02 11:05:23 - 2005-11-01 11:05:23 | 0               | 0             | test description 6  |
      | event7        | 2035-10-02 11:05:23 - 2005-11-01 11:05:23 | 1               | 1             | test description 7  |
      | event8        | 2035-10-02 11:05:23 - 2005-11-01 11:05:23 | 0               | 1             | test description 8  |
      | Event 3       | 2035-10-02 13:00:00 - 2005-10-01 15:00:00 | 1               | 0             | test description 3  |
      | Event, and 4  | 2035-10-01 13:00:00 - 2005-10-01 13:00:00 | 1               | 0             | test description 4  |
    When I visit "/events"
    Then I break
    Then I should see "Coming events":
      | h2           | Dates field |
      | Event, and 4 | 1 Oct 2035  |
      | Event2       | 2 Oct 2035  |
      | Event 3      | 2 Oct 2035  |
      | Event1       | 30 Dec 2035 |
    When I click "event2"
    Then I should see "test description 2"