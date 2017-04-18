@api
Feature: Event recordings
  In order to manage recordings from a session
  As any user with access to manage recordings
  I need to work with recordings from the sessions of an event

  Background:
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
    Given "Session type" terms:
      | name       |
      | Meditation |
      | Teaching   |
    Given session content:
      | title           | field_datetime      | field_media      | field_session_type |
      | Session1        | 2005-12-30 11:00:00 | Audio1, Video1   | Teaching           |
      | Session2        | 2005-10-01 11:05:00 | Audio2, Audio3   | Teaching           |
      | Session3        | 2005-10-01 14:00:00 | Video2           | Teaching           |
      | Session4        | 2005-10-01 13:00:00 |                  | Meditation         |
      | Session5        | 2035-10-01 14:00:00 |                  | Meditation         |
      | Session6        | 2035-10-01 15:00:00 |                  | Meditation         |
      | Session7        | 2005-10-02 13:00:00 | Video2           | Teaching           |
      | Session8        | 2035-10-02 11:00:00 |                  | Meditation         |
      | Session9        | 2035-10-01 17:30:00 |                  | Meditation         |
      | Session10       | 2035-10-02 11:00:00 |                  | Meditation         |
      | Session11       | 2005-10-01 14:00:00 |                  | Meditation         |
      | Session12       | 2005-10-01 17:30:00 |                  | Meditation         |
    Given "event" content:
      | title  | field_sessions   |
      | Event1 | Session1, Session2, Session3, Session4, Session5, Session6, Session7, Session8, Session9 |
      | Event2 | Session11, Session12                                         |
      | Event3 |                                                            |

  Scenario: Viewing existing sessions
    Given I am logged in as an administrator
    When I visit "admin/content"
    And I click "Event1"
    And I click "Manage recordings"
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Fri 30 Dec, 11:00am |
      | Session2    | Sat 1 Oct, 11:05am  |
      | Session3    | Sat 1 Oct, 2:00pm   |
      | Session4    | Sat 1 Oct, 1:00pm   |
      | Session5    | Mon 1 Oct, 2:00pm   |
      | Session6    | Mon 1 Oct, 3:00pm   |
      | Session7    | Sun 2 Oct, 1:00pm   |
      | Session8    | Tue 2 Oct, 11:00am  |
      | Session9    | Mon 1 Oct, 5:30pm   |
    When I press the "Edit" button in the "Session2" row
    And I fill in "field_sessions[form][inline_entity_form][entities][1][form][title][0][value]" with "Session2renamed"
    And I fill in "field_sessions[form][inline_entity_form][entities][1][form][field_datetime][0][value][date]" with "2005-10-02"
    And I fill in "field_sessions[form][inline_entity_form][entities][1][form][field_datetime][0][value][time]" with "16:00"
    And I select "Meditation" from "field_sessions[form][inline_entity_form][entities][1][form][field_session_type]"
    And I press the "Update session" button
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Fri 30 Dec, 11:00am |
      | Session2renamed    | Sun 2 Oct, 4:00pm  |
      | Session3    | Sat 1 Oct, 2:00pm   |
      | Session4    | Sat 1 Oct, 1:00pm   |
      | Session5    | Mon 1 Oct, 2:00pm   |
      | Session6    | Mon 1 Oct, 3:00pm   |
      | Session7    | Sun 2 Oct, 1:00pm   |
      | Session8    | Tue 2 Oct, 11:00am  |
      | Session9    | Mon 1 Oct, 5:30pm   |
    When I press the "Remove" button in the "Session3" row
    And I press the "Remove" button in the "Are you sure you want to remove Session3" row
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Fri 30 Dec, 11:00am |
      | Session2renamed    | Sun 2 Oct, 4:00pm  |
      | Session4    | Sat 1 Oct, 1:00pm   |
      | Session5    | Mon 1 Oct, 2:00pm   |
      | Session6    | Mon 1 Oct, 3:00pm   |
      | Session7    | Sun 2 Oct, 1:00pm   |
      | Session8    | Tue 2 Oct, 11:00am  |
      | Session9    | Mon 1 Oct, 5:30pm   |
    When I press the "Save and keep published" button
    And I click "Manage recordings"
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Fri 30 Dec, 11:00am |
      | Session2renamed    | Sun 2 Oct, 4:00pm  |
      | Session4    | Sat 1 Oct, 1:00pm   |
      | Session5    | Mon 1 Oct, 2:00pm   |
      | Session6    | Mon 1 Oct, 3:00pm   |
      | Session7    | Sun 2 Oct, 1:00pm   |
      | Session8    | Tue 2 Oct, 11:00am  |
      | Session9    | Mon 1 Oct, 5:30pm   |
    When I press the "Add new session" button
    And I fill in "field_sessions[form][inline_entity_form][title][0][value]" with "SessionNew"
    And I fill in "field_sessions[form][inline_entity_form][field_datetime][0][value][date]" with "2005-10-02"
    And I fill in "field_sessions[form][inline_entity_form][field_datetime][0][value][time]" with "17:00"
    And I select "Meditation" from "field_sessions[form][inline_entity_form][field_session_type]"
    And I press the "Create session" button
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Fri 30 Dec, 11:00am |
      | Session2renamed    | Sun 2 Oct, 4:00pm  |
      | Session4    | Sat 1 Oct, 1:00pm   |
      | Session5    | Mon 1 Oct, 2:00pm   |
      | Session6    | Mon 1 Oct, 3:00pm   |
      | Session7    | Sun 2 Oct, 1:00pm   |
      | Session8    | Tue 2 Oct, 11:00am  |
      | Session9    | Mon 1 Oct, 5:30pm   |
      | SessionNew  | Sun 2 Oct, 5:00pm   |
    When I press the "Save and keep published" button
    And I click "Manage recordings"
    Then I should see "Sessions in an inline form":
      | Title field | Datetime field      |
      | Session1    | Fri 30 Dec, 11:00am |
      | Session2renamed    | Sun 2 Oct, 4:00pm  |
      | Session4    | Sat 1 Oct, 1:00pm   |
      | Session5    | Mon 1 Oct, 2:00pm   |
      | Session6    | Mon 1 Oct, 3:00pm   |
      | Session7    | Sun 2 Oct, 1:00pm   |
      | Session8    | Tue 2 Oct, 11:00am  |
      | Session9    | Mon 1 Oct, 5:30pm   |
      | SessionNew  | Sun 2 Oct, 5:00pm   |
