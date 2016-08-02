@api
Feature: Site Installation
  In order to do anything
    Drupal and key modules needs to be installed 
  
  Background;
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Drupal is installed
    Given I am on "/core/install.php"
    Then I should see "Drupal already installed"

  Scenario: Flysystem is installed
    When I visit "admin/config/media/file-system/flysystem"
    Then the response status code should be 200 

  Scenario: File(field) Paths is installed
    When I visit "admin/config/media/file-system/filefield-paths"
    Then the response status code should be 200 

  Scenario: Media entity is installed
    When I visit "admin/structure/media"
    Then the response status code should be 200 
    
  Scenario: Media entity audio & Video embed field are installed
    Given I am on "admin/structure/media/add"
    Then the "select[name='type'] option[value='audio']" element contains "Audio"
    Then the "select[name='type'] option[value='video_embed_field']" element contains "Video embed field"
