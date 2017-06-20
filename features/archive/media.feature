@api
Feature: Media archive
  In order to catalogue and make available teachings
    we need to track the recordings we have 
  
  Background:
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Media youtube type exists
    When I visit "media/add/youtube"
    Then the response status code should be 200

  Scenario: Media youtube display
    Given "youtube" "media" entities:
    | name    | field_media_video_embed_field               |
    | Gangnam | https://www.youtube.com/watch?v=9bZkp7q19f0 |
    When I visit "admin/content/media"
    Then I should see "Gangnam"
    When I click "Edit" in the "Gangnam" row
    Then the "field_media_video_embed_field[0][value]" field should contain "https://www.youtube.com/watch?v=9bZkp7q19f0"

  Scenario: Media audio type exists
    When I visit "media/add/audio"
    Then the response status code should be 200

  Scenario: Media audio display
    Given a "file" entity:
      | uri               |
      | dropboxwebarchive://test.mp3 |
    Given I am viewing an "audio" "media" entity:
      | name          | Test audio   |
      | field_dropbox | test.mp3     |
    # Test src attribute ends with flysystem path
    Then I should see an "audio source[src$='/_flysystem/dropboxwebarchive/test.mp3']" element
