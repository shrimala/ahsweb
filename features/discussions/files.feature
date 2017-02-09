@api @skip
Feature: File attachments
  In order to share files as part of discussions
  As any user allowed to participate in discussions
  I need to be able to upload files and view uploaded files.

  Background:
    Given I am logged in as an "authenticated user"

#fails on subsequent reruns because files are not cleaned up
  Scenario: Upload and view files
    Given a "discussion" with the title "Test"
    And I am on "/discuss/test"
    When I attach the file "testfile.txt" to "files[field_files_0][]"
    And I press "Upload"
    And I press "Save"
    Then I should see the link "testfile.txt"
    When I visit "_flysystem/dropboxwebarchive/discussions/test/testfile.txt"
    Then I should see "test file contents"
