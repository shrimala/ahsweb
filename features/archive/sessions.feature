@api
Feature: Sessions archive
  In order to track teachings
    we need a record of teaching sessions 
  
  Background:
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Sessions content type exists
    When I visit "node/add/session"
    Then I should see the response status code should be 200 

  Scenario: Sessions have URL archives/year/month/day/hourminute/title
    Given session content:
      | title    | field_datetime   |
      | session1 | 2005-12-30 13:00 |
    When I visit "archive/sessions/2005/12/30/1300/session1"
    Then the response status code should be 200 
