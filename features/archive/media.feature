@api @skip
Feature: Media archive
  In order to catalogue and make available teachings
    we need to track the recordings we have 
  
  Background:
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Media youtube type exists
    When I visit "media/add/youtube"
    Then I should see the response status code should be 200

  Scenario: Media youtube display
    Given "youtube" "media" entities:
    | name    | field_media_video_embed_field               |
    | Gangnam | https://www.youtube.com/watch?v=9bZkp7q19f0 |
    When I visit "admin/content/media"
    Then I should see "Gangnam"
    When I click "Edit" in the "Gangnam" row
    Then the field_media_video_embed_field field should contain "https://www.youtube.com/watch?v=9bZkp7q19f0"
  
   Scenario: Media youtube display 
    Given a "file" entity:
    | uri               | 
    | dropboxarchive://test.mp3 |
    Given I am viewing an "audio" "media":
    | name          | Test audio   |
    | field_dropbox | test.mp3     |
    Then I should see the link "test.mp3"
