@api
Feature: Test EntityContext
  In order to prove the Behat EntityContext has loaded
  As a developer
  I need to run steps that need it

  Background:
	Given I am logged in as an administrator

  Scenario: Test single bundleless entity in "Given a :entity_type entity" 
    Given a "user" entity:
	| name             | mail                     |
	| entity_testuser1 | testuser1@testdomain.com |
	When I am at "admin/people"
    Then I should see "entity_testuser1"
	When I click "entity_testuser1"
	And I click "Edit"
	Then the "mail" field should contain "testuser1@testdomain.com"

  Scenario: Test entities have been cleaned up after previous scenario
	When I am at "admin/people"
    Then I should not see "entity_testuser1"

  Scenario: Test multiple bundleless entities in "Given :entity_type entities" 
    Given "user" entities:
	| name             | mail                     |
	| entity_testuser1 | testuser1@testdomain.com |
	| entity_testuser2 | testuser2@testdomain.com |
	When I am at "admin/people"
    Then I should see "entity_testuser1"
	When I click "entity_testuser1"
	And I click "Edit"
	Then the "mail" field should contain "testuser1@testdomain.com"
	When I am at "admin/people"
    Then I should see "entity_testuser2"
	When I click "entity_testuser2"
	And I click "Edit"
	Then the "mail" field should contain "testuser2@testdomain.com"
	
  Scenario: Test bundleless entity in "Given I am viewing a :entity_type entity with the :label_name :label" 
    Given I am viewing a "user" entity with the "name" "entity_testuser1"
	Then I should see "entity_testuser1" in the ".page-title" element

  Scenario: Test bundled entity in "Given I am viewing a :bundle :entity_type entity with the :label_name :label" 
    Given I am viewing an "article" "node" entity with the "title" "entity_testnode1"
	Then I should see "entity_testnode1" in the ".page-title" element

  Scenario: Test bundleless entity in "Given I am viewing a :entity_type entity:" 
    Given I am viewing a "user" entity:
	| name | entity_testuser1        |
	| mail | testuser1@testdomain.com |
	Then I should see "entity_testuser1" in the ".page-title" element
	When I click "Edit"
	Then the "mail" field should contain "testuser1@testdomain.com"
