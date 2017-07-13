@api
Feature: Mail sending can be tested
  In order to make email teachings accessible later
    we need an archive of them 
  
  Background:
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Email teachings content type exists
    When I visit "node/add/email_teaching"
    Then the response status code should be 200 

  Scenario: Email teachings have URL archive/emails/year/month/day/titleword1-titleword2-etc
    Given email_teaching content:
      | title           | created      |
      | Email1          | 2005-12-30 13:12:01 |
      | Email2          | 2005-10-01 11:05:00 |
      | Email3          | 2005-10-01 00:00:00 |
      | Email 4         | 2005-10-01 13:00:00 |
      | Email, and 5    | 2005-10-01 13:00:00 |
    When I visit "archive/emails/2005/12/30/email1"
    When I visit "archive/emails/2005/10/01/email2"
    When I visit "archive/emails/2005/10/01/email3"
    When I visit "archive/emails/2005/10/01/email-4"
    When I visit "archive/emails/2005/10/01/email-and-5"
    Then the response status code should be 200 
