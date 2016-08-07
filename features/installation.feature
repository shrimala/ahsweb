@api
Feature: Site Installation
  In order to do anything
    Drupal and key modules needs to be installed 
  
  Background:
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
    Then the "select[name='type'] option[value='audio']" element should not contain "Nonexistent media type"
    Then the "select[name='type'] option[value='audio']" element should contain "Audio"
    Then the "select[name='type'] option[value='video_embed_field']" element should contain "Video embed field"

  Scenario: Paragraphs is installed
    When I visit "admin/structure/paragraphs_type"
    Then the response status code should be 200 

  Scenario: Pathauto is installed
    When I visit "admin/config/search/path/patterns"
    Then the response status code should be 200    

  Scenario: Token is installed
    When I visit "admin/structure/display-modes/view/manage/node.token"
    Then the response status code should be 200
