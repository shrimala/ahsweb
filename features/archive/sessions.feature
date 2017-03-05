@api
Feature: Sessions archive
  In order to track teachings
    we need a record of teaching sessions 
  
  Background:
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Sessions content type exists
    When I visit "node/add/session"
    Then the response status code should be 200 

  @skiplocal
  Scenario: Sessions have URL archive/sessions/year/month/day/hourminute/titleword1-titleword2-etc
    Given session content:
      | title           | field_datetime      |
      | Session1        | 2005-12-30 13:00:00 |
      | Session2        | 2005-10-01 11:05:00 |
      | Session3        | 2005-10-01 13:00:23 |
      | Session 4       | 2005-10-01 13:00:00 |
      | Session, and 5  | 2005-10-01 13:00:00 |
    When I visit "archive/sessions/2005/12/30/1300/session1"
    When I visit "archive/sessions/2005/10/01/1105/session2"
    When I visit "archive/sessions/2005/10/01/1300/session3"
    When I visit "archive/sessions/2005/10/01/1300/session-4"
    When I visit "archive/sessions/2005/10/01/1300/session-and-5"
    Then the response status code should be 200 
