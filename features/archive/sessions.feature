@api
Feature: Sessions archive
  In order to access teachings
  As any student
  I need to access event sessions
  
  Background:
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Sessions content type exists
    When I visit "node/add/session"
    Then the response status code should be 200

  # not currently using session aliases due to time zone complications
  @skip
  Scenario: Sessions have URL archive/sessions/year/month/day/hourminute/titleword1-titleword2-etc
    Given session content:
      | title           | field_datetime      |
      | Session1        | 2005-12-30 13:00:00 |
      | Session2        | 2005-10-01 11:05:00 |
      | Session3        | 2005-10-01 13:00:23 |
      | Session 4       | 2005-10-01 13:00:00 |
      | Session, and 5  | 2005-10-01 13:00:00 |
    Then I break
    When I visit "archive/sessions/2005/12/30/1300/session1"
    When I visit "archive/sessions/2005/10/01/1105/session2"
    When I visit "archive/sessions/2005/10/01/1300/session3"
    When I visit "archive/sessions/2005/10/01/1300/session-4"
    When I visit "archive/sessions/2005/10/01/1300/session-and-5"
    Then the response status code should be 200

  @skip
  Scenario: Sessions have URL archive/sessions/year/month/day/hourminute/titleword1-titleword2-etc
    Given session content:
      | title               | field_datetime      |
      | 2005-12-30 04:00:00 | 2005-12-30 04:00:00 |
      | 2005-12-30 04:15:00 | 2005-12-30 04:15:00 |
      | 2005-12-30 04:30:00 | 2005-12-30 04:30:00 |
      | 2005-12-30 04:45:00 | 2005-12-30 04:45:00 |
      | 2005-12-30 14:00:00 | 2005-12-30 14:00:00 |
      | 2005-12-30 14:15:00 | 2005-12-30 14:15:00 |
      | 2005-12-30 14:30:00 | 2005-12-30 14:30:00 |
      | 2005-12-30 14:45:00 | 2005-12-30 14:45:00 |
      | 2005-06-30 04:00:00 | 2005-06-30 04:00:00 |
      | 2005-06-30 04:15:00 | 2005-06-30 04:15:00 |
      | 2005-06-30 04:30:00 | 2005-06-30 04:30:00 |
      | 2005-06-30 04:45:00 | 2005-06-30 04:45:00 |
      | 2005-06-30 14:00:00 | 2005-06-30 14:00:00 |
      | 2005-06-30 14:15:00 | 2005-06-30 14:15:00 |
      | 2005-06-30 14:30:00 | 2005-06-30 14:30:00 |
      | 2005-06-30 14:45:00 | 2005-06-30 14:45:00 |
    Then I break

  Scenario: Sessions show their fields
    Given event content:
      | title           | field_datetime      |
      | Event1          | 2005-12-29 04:00:00 |
    And "Session type" terms:
      | name       |
      | Teaching   |
    And teacher content:
      | title        |
      | Lama Shenpen |
      | Dashu        |
    And "youtube" "media" entities:
      | name    | field_media_video_embed_field               |
      | Video1  | https://www.youtube.com/watch?v=9bZkp7q19f0 |
    And I am viewing a session content:
      | title              | Session1            |
      | field_datetime     | 2005-12-30 14:00:00 |
      | field_event        | Event1              |
      | field_session_type | Teaching            |
      | field_leader       | Lama Shenpen        |
      | field_media        | Video1              |
    Then I should see "Event1" in the "Event field"
    And I should see "Teaching" in the "Session type field"
    And I should see "Lama Shenpen" in the "Leader field"
    And I should see an "iframe[src='https://www.youtube.com/embed/9bZkp7q19f0?autoplay=0&start=0&rel=0']" element

