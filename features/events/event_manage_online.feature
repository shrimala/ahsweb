@api
Feature: Event sessions
  In order to participate in an event remotely or retrospectively
  As any user with access to event sessions
  I need to see coming event sessions and past sessions with recordings

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
    Given session_type terms:
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

  Scenario: "Manage online" is using admin theme
    Given I am logged in as an administrator
    When I visit "admin/content"
    And I click "Event1"
    And I click "Manage online"
    Then I should see a "#block-seven-page-title" element

  Scenario: Adding a first session to an event using "Manage online"
    Given I am logged in as an administrator
    When I visit "admin/content"
    And I click "Event3"
    And I click "Manage online"
    And I select "Teaching" from "field_sessions[0][inline_entity_form][field_session_type]"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][date]" with "2035-10-01"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][time]" with "16:00"
    And I fill in "field_sessions[0][inline_entity_form][title][0][value]" with "Test session"
    And I press the "Save and keep published" button
    And I click "Online"
    Then I should see "Next session":
      | Title field  | Datetime field           |
      | Test session | Monday 1 October, 4:00pm |

  Scenario: Adding a first session using "Manage online" without specifying title
    Given I am logged in as an administrator
    When I visit "admin/content"
    And I click "Event3"
    And I click "Manage online"
    And I select "Teaching" from "field_sessions[0][inline_entity_form][field_session_type]"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][date]" with "2035-10-01"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][time]" with "16:00"
    And I press the "Save and keep published" button
    And I click "Online"
    Then I should see "Next session":
      | Title field  | Datetime field            |
      | Teaching      | Monday 1 October, 4:00pm |

  Scenario: Session type is a required field
    Given I am logged in as an administrator
    When I visit "admin/content"
    And I click "Event3"
    And I click "Manage online"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][date]" with "2035-10-01"
    And I fill in "field_sessions[0][inline_entity_form][field_datetime][0][value][time]" with "16:00"
    And I fill in "field_sessions[0][inline_entity_form][title][0][value]" with "Test session"
    And I press the "Save and keep published" button
    And I click "Online"
    Then I should not see "Test session"

  Scenario: Adding an additional session using "Manage online"
    Given I am logged in as an administrator
    When I visit "admin/content"
    And I click "Event2"
    And I click "Manage online"
    And I press the "field_sessions_add_more" button
    And I fill in "field_sessions[2][inline_entity_form][field_datetime][0][value][date]" with "2035-10-01"
    And I fill in "field_sessions[2][inline_entity_form][field_datetime][0][value][time]" with "16:00"
    And I fill in "field_sessions[2][inline_entity_form][title][0][value]" with "Test session"
    And I select "Teaching" from "field_sessions[2][inline_entity_form][field_session_type]"
    And I press the "Save and keep published" button
    And I click "Online"
    Then I should see "Next session":
      | Title field  | Datetime field            |
      | Test session  | Monday 1 October, 4:00pm |
