@api @skip
Feature: Event sessions
  In order to participate in an event remotely or retrospectively
  As any user with access to event sessions
  I need to see coming event sessions and past sessions with recordings

  Background:
    Given I am logged in as an administrator

  Scenario: Sessions for an event are listed on the "Online" page
    Given a "file" entity:
      | uri               |
      | dropboxwebarchive://test1.mp3 |
      | dropboxwebarchive://test2.mp3 |
      | dropboxwebarchive://test3.mp3 |
      | dropboxwebarchive://test4.mp3 |
    Given "audio" "media" entities:
      | name   | field_dropbox |
      | Audio1 | test1.mp3      |
      | Audio2 | test2.mp3      |
      | Audio3 | test3.mp3      |
      | Audio4 | test4.mp3      |
    Given "youtube" "media" entities:
      | name    | field_media_video_embed_field               |
      | Video1  | https://www.youtube.com/watch?v=9bZkp7q19f0 |
      | Video2  | https://www.youtube.com/watch?v=9bZkp7q19f0 |
    Given session_type terms:
      | name       |
      | Meditation |
      | Teaching   |
    Given teacher content:
      | title        |
      | Lama Shenpen |
      | Dashu        |
    Given session content:
      | title           | field_datetime      | field_media      | field_session_type | field_leader        |
      | Session1        | 2005-12-30 11:00:00 | Audio1, Video1   | Teaching           | Lama Shenpen        |
      | Session2        | 2005-10-01 11:05:00 | Audio2, Audio3   | Teaching           | Lama Shenpen, Dashu |
      | Session3        | 2005-10-01 14:00:00 | Video2           | Teaching           | Dashu               |
      | Session4        | 2005-10-01 13:00:00 |                  | Meditation         |                     |
      | Session5        | 2035-10-01 14:00:00 |                  | Meditation         |                     |
      | Session6        | 2035-10-01 15:00:00 |                  | Meditation         |                     |
      | Session7        | 2005-10-02 13:00:00 | Video2           | Teaching           |                     |
      | Session8        | 2035-10-02 11:00:00 |                  | Meditation         |                     |
      | Session9        | 2035-10-01 17:30:00 |                  | Meditation         |                     |
      | Session10       | 2035-10-02 11:00:00 |                  | Meditation         |                     |
      | Session11       | 2005-12-01 14:00:00 |                  | Meditation         |                     |
      | Session12       | 2005-12-01 17:30:00 |                  | Meditation         |                     |
    Given "event" content:
      | title  | field_sessions   |
      | Event1 | Session1, Session2, Session3, Session4, Session5, Session6, Session7, Session8, Session9 |
      | Event2 | Session11, Session12                                         |
      | Event3 |                                                              |
    When I visit "admin/content"
    And I click "Event1"
    And I click "Online"
    Then I should see the heading "Next"
    And I should see "Next session":
      | Title field  | Datetime field            |
      | Session5      | Monday 1 October, 2:00pm |
    And I should see the heading "Coming"
    And I should see "Coming session day groupings":
      | contains          |
      | Monday 1 October  |
      | Tuesday 2 October |
    And I should see "Coming sessions":
      | Title field  | Datetime field            |
      | Session6      | 3:00pm           |
      | Session9      | 5:30pm           |
      | Session8      | 11:00am          |
    And I should see the heading "Recordings"
    And I should see "Finished session headings":
      | Title field | Datetime field     |
      | Session2    | Saturday 1 October, 11:05am    |
      | Session3    | Saturday 1 October, 2:00pm     |
      | Session7    | Sunday 2 October, 1:00pm     |
      | Session1    | Friday 30 December, 11:00am    |
    And I should see "Finished session bodies":
      | Title field | Session type field | Leader field       | audio source | iframe |
      | No          | Teaching           | Lama Shenpen Dashu | Yes          | No     |
      | No          | Teaching           | Dashu              | No           | Yes    |
      | No          | Teaching           |                    | No           | Yes    |
      | No          | Teaching           | Lama Shenpen       | Yes          | Yes    |
    When I select "- Any -" from "field_media_target_id"
    And I press the "Apply" button
    Then I should see "Finished session headings":
      | Title field | Datetime field     |
      | Session2    | Saturday 1 October, 11:05am    |
      | Session4    | Saturday 1 October, 1:00pm     |
      | Session3    | Saturday 1 October, 2:00pm     |
      | Session7    | Sunday 2 October, 1:00pm       |
      | Session1    | Friday 30 December, 11:00am    |
    #Clicking opens panels, rather than going to new URL
    When I click "Session2"
    Then I should see the heading "Recordings"
    When I visit "admin/content"
    And I click "Event2"
    And I click "Online"
    Then I should not see the heading "Next"
    And I should not see the heading "Coming"
    And I should see no "Finished session headings" elements
    When I select "- Any -" from "field_media_target_id"
    And I press the "Apply" button
    Then I should see the heading "Recordings"
    Then I should see "Finished session headings":
      | Title field | Datetime field               |
      | Session11       | Thursday 1 December, 2:00pm |
      | Session12       | Thursday 1 December, 5:30pm |
    When I click "Full details"
    Then I should see "Session11" in the "h1.page-header" element
    When I visit "admin/content"
    And I click "Event3"
    And I click "Online"
    Then I should not see the heading "Next"
    And I should not see the heading "Coming"
    And I should not see the heading "Recordings"
    When I should not see a "field_media_target_id" element
