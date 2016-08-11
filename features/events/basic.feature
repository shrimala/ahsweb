@api
Feature: Events listings
  In order to offer events and give context for teaching sessions
    we need a record of events 
  
  Background:
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Events content type exists
    When I visit "node/add/event"
    Then the response status code should be 200 

  Scenario: Events have URL events/year/month/day/titleword1-titleword2-etc
    Given event content:
      | title           | field_datetime      |
      | event1        | 2005-12-30 00:00:00 |
      | event2        | 2005-10-01 11:05:23 |
      | Event 3       | 2005-10-01 13:00:00 |
      | Event, and 4  | 2005-10-01 13:00:00 |
    When I visit "events/2005/12/30/event1"
    When I visit "events/2005/10/01/event2"
    When I visit "events/2005/10/01/event-3"
    When I visit "events/2005/10/01/event-and-4"
    Then the response status code should be 200 
