@api
Feature: Information pages
    In order to offer informtion to the public
    We need static content pages

  Scenario: Information is accessible to anonymous at alias
    Given I am not logged in
    And information content:
      | title       | path        |
      | abra page   | /abracadabra |
    When I visit "abracadabra"
    Then I am visiting "/abracadabra"
