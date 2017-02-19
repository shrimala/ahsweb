@api
Feature: Titles are sanitised
  In order to have titles that read easily and behave well in urls
  As any user allowed to edit a discussion
  I need to have my discussion titles sanitised.

  #Sansitisation is handled by the ahs_miscellaneous module.

  Background:
    Given I am logged in as an "authenticated user"

  Scenario: Discussion title is capitalised and sanitised
    When I visit "/discuss/add"
    And I fill in "title[0][value]" with " test / Title something"
    And I press the "Save" button
    Then I am visiting "/discuss/test-title-something"
    And the "title[0][value]" field should contain exactly "Test Title something"
    When I fill in "title[0][value]" with " test / Title something"
    And I press the "Save" button
    Then I am visiting "/discuss/test-title-something"
    And the "title[0][value]" field should contain exactly "Test Title something"
    When I fill in "title[0][value]" with "TEST"
    And I press the "Save" button
    Then I am visiting "/discuss/test"
    And the "title[0][value]" field should contain exactly "TEST"
    When I fill in "title[0][value]" with "TEST SOMETHING"
    And I press the "Save" button
    Then I am visiting "/discuss/test-something"
    And the "title[0][value]" field should contain exactly "Test something"