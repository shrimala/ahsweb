@api @skip
Feature: Media archive
  In order to catalogue and make available teachings
    we need to track the recordings we have 
  
  Background:
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Media youtube type exists
    When I visit "media/add/youtube"
    Then I should see the response status code should be 200

  Scenario: Media youtube can be created
    Given "youtube" "media":
    | name    | field_media_video_embed_field               |
    | Gangnam | https://www.youtube.com/watch?v=9bZkp7q19f0 |
    When I visit "admin/content/media"
    Then I should see "Gangnam"
    When I click "Edit" in the "Gangnam" row
    Then the field_media_video_embed_field field should contain "https://www.youtube.com/watch?v=9bZkp7q19f0"
  
  
   Scenario: Test a file reference 
    Given a "file" entity:
    | uri               | 
    | public://test.txt |
    Given I am viewing an "article":
    | title        | test article |
    | field_myfile | test.txt     |
    Then I should see "test.txt"
    When I am at "admin/content/files"
        Then I should see "test.txt"
