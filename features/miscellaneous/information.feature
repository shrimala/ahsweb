@api @test
Feature: Information pages
    In order to offer informtion to the public
    We need static content pages

  Scenario: Information is accessible to anonymous at alias
    Given information content:
      | title       | path        |
      | abra page   | /abracadabra |
    When I visit "abracadabra"
    Then the response status code should be 200 
