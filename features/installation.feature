@api
Feature: Site Installation
  In order to do anything
  Drupal and key modules needs to be installed, and no errors should be present 
  
  Background:
    Given I am logged in as a user with the "administrator" role
    
  Scenario: Drupal is installed
    Given I am on "/core/install.php"
    Then I should see "Drupal already installed"
  
  @skip
  Scenario: Config has synchronised 
    When I visit "admin/config/development/configuration"
    Then I should not see "The following items in your active configuration have changes since the last import that may be lost on the next import"
    And I should see "There are no configuration changes to import."
    
  Scenario: Database updates have run
    When I visit "admin/reports/status"
    Then I should see "Up to date" in the "Database updates" row

  @skip
  Scenario: Schema is up to date
    When I visit "admin/reports/status"
    Then I should see "Up to date" in the "Entity/field definitions" row

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

  Scenario: Modules are installed
    Given the "Flysystem" module is installed
    Given the "Flysystem Dropbox" module is installed
    Given the "Media entity" module is installed
    Given the "Media entity audio" module is installed
    Given the "Video Embed Field" module is installed
    Given the "Video Embed Media" module is installed
    Given the "File (Field) Paths" module is installed
    Given the "File Field Sources" module is installed
    Given the "Paragraphs" module is installed
    Given the "Pathauto" module is installed
    Given the "Token" module is installed
    Given the "Chaos tools" module is installed
    Given the "Email Registration" module is installed
    Given the "Corresponding Entity References" module is installed
    Given the "Discussions" module is installed
    Given the "User management" module is installed
    Given the "Miscellaneous" module is installed
    Given the "Changes" module is installed
    Given the "Field Group" module is installed
    Given the "Chosen Field" module is installed
    Given the "Diff" module is installed
    Given the "Block Visibility Groups" module is installed
    Given the "Redirect 403 to User Login" module is installed
    Given the "Linkit" module is installed