@api
Feature: Sangha name
  In order to accomodate nicknames and Refuge names
  As a user
  I need to be able to indicate the name I prefer to be known by

  Scenario: Sangha name can be edited
    Given users:
    | name        | mail            | field_first_name | field_last_name |
    | Fred Bloggs | fred@bloggs.com | Fred             | Bloggs          |
    Given I am logged in as an administrator
    When I visit "/admin/people"
    Then I should see "Fred Bloggs"
    When I click "Edit" in the "Fred Bloggs" row
    Then the "Sangha name" field should contain "Fred Bloggs"
    When I fill in "Sangha name" with "Freddy Boggs"
    And I press "Save"
    Then I am visiting "/admin/people"
    And I should see "Freddy Boggs"