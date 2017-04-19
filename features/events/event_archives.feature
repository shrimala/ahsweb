@api
Feature: Event archives
  In order to access recordings from past events
  As any user with access to the archives
  I need to see a list of past events

  Background:
    Given venue content:
      | title            |
      | Hermitage        |
    Given teacher content:
      | title            |
      | Lama Shenpen     |
    Given event content:
      | title           | field_datetime      | field_venue      | field_leader |
      | Event1          | 2005-12-29 12:00:00 | Hermitage        | Lama Shenpen |
      | Event2          | 2035-12-29 12:00:00 | Hermitage        | Lama Shenpen |
    And I am logged in as an administrator
@test
  Scenario: See a list of past events
    When I visit "archives/events"
    Then I should see the heading "Archive of events"
    And I should see "Views rows":
      | Title field | Datetime field | Venue field | Leader field |
      | Event1      | 29 Dec 2005    | Hermitage   | Lama Shenpen |
    And I should not see "Event2"