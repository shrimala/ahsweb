@api
Feature: Discussion notifications
  In order to speed discussion conversations
  As a user commenting on a discussion
  I need to get email notifications.

  Scenario: Users are notified about comments and change records
    Given users:
      | name         | mail              | status |
      | Fred Bloggs  | fred@example.com  | 1      |
      | Jane Doe     | jane@example.com  | 1      |
      | Hans Schmidt | hans@example.com  | 1      |
    Given a "discussion" with the title "Test0"
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/test0"
    When I fill in "title[0][value]" with "Test1"
    And I press "Save"
    # Fred gets no notification  of his change record comment
    Then no new email is sent
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test1"
    When I comment "Jane's #1 comment"
    # Fred receives a notification of Jane's comment because of his
    # change record comment. Jane gets no notification of her own comment.
    Then a new email is sent:
      | to   | subject | body              |
      | Fred | Test1   | Jane's #1 comment |
    Given I am logged in as "Hans Schmidt"
    And I visit "/discuss/test1"
    And I comment "Hans' #1 comment"
    # Jane gets a notification of Hans' comment because of her comment
    # Multiple emails can be sent.
    Then new emails are sent:
      | to   | subject | body             |
      | Fred | Test1   | Hans' #1 comment |
      | Jane | Test1   | Hans' #1 comment |
    Given I am logged in as "Jane Doe"
    And I visit "/discuss/test1"
    # Implicitly unselect Hans
    And I select "Fred Bloggs" from "field_participants[]"
    And I press "Save"
    # No one gets a notification of a change record comment
    Then no new email is sent
    And I comment "Jane's #2 comment"
    # Hans gets no notification as he is not listed as a participant.
    Then a new email is sent:
      | to   | subject | body              |
      | Fred | Test1   | Jane's #2 comment |
  @test
  Scenario: Comment notifications have useful content
    Given users:
      | name         | mail              | status |
      | Fred Bloggs  | fred@example.com  | 1      |
      | Jane Doe     | jane@example.com  | 1      |
    Given discussion content:
      | title       | field_parents | body           |
      | grandparent |               |                |
      | parent      | grandparent   |                |
      | child       | parent        | Some body text |
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/grandparent/parent/child"
    When I comment "Fred's comment"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/grandparent/parent/child"
    And I comment "Jane's comment"
    And I press "Save"
    Then a new email is sent:
      | to   | subject | body           | body        | body                 | body  |
      | Fred | child   | Fred's comment | Fred Bloggs | grandparent / parent | child |
    Given I am logged in as "Fred Bloggs"
    When I follow the link to "/discuss/" from the email to Fred
    Then I am visiting "/discuss/grandparent/parent/child"
    And I should see "Some body text"
