@api
Feature: Discussion comments and change records
  In order to engage in a discussion
  As a user viewing a discussion
  I need to comment and track changes to the discussion.

  Scenario: Comment and edit in various sequences; first action is edit
    Given I am logged in as an "authenticated user"
    And a "discussion" with the title "Test1"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  |
      | Started this discussion. | No            |
    When I fill in "title[0][value]" with "Test2"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
    When I comment "Comment 1"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
      | Comment 1                | No            | No               |
    When I comment "Comment 2"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
      | Comment 1                | No            | No               |
      | Comment 2                | No            | No               |
    When I fill in "title[0][value]" with "Test3"
    And I check the box "Help wanted"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
      | Comment 1                | No            | No               |
      | Comment 2                | No            | No               |
      |                          | Yes           |                  |
    When I fill in "title[0][value]" with "Test4"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
      | Comment 1                | No            | No               |
      | Comment 2                | No            | No               |
      |                          | Yes           |                  |
    When I fill in "title[0][value]" with "Test5"
    And I fill in "comment[value]" with "Comment 3"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
      | Comment 1                | No            | No               |
      | Comment 2                | No            | No               |
      | No                       | Yes           |                  |
      | Comment 3                | No            | No               |
    When I fill in "title[0][value]" with "Test6"
    And I fill in "comment[value]" with "Comment 4"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
      | Comment 1                | No            | No               |
      | Comment 2                | No            | No               |
      | No                       | Yes           |                  |
      | Comment 3                | No            | No               |
      | No                       | Yes           |                  |
      | Comment 4                | No            | No               |

  Scenario: Comment and edit in various sequences; first action is comment
    Given I am logged in as an "authenticated user"
    And I am viewing a "discussion" with the title "Test1"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | -                |
    When I comment "Comment 1"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | -                |
      | Comment 1                | No            | No               |
    When I fill in "title[0][value]" with "Test2"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | Comment 1                | No            | No               |
      | No                       | Yes           |                  |

  Scenario: Comment and edit in various sequences; first action is comment and edit
    Given I am logged in as an "authenticated user"
    And I am viewing a "discussion" with the title "Test1"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | -                |
    When I fill in "title[0][value]" with "Test2"
    And I fill in "comment[value]" with "Comment 1"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
      | Comment 1                | No            | No               |
    When I fill in "title[0][value]" with "Test3"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
      | Comment 1                | No            | No               |
      | No                       | Yes           |                  |

  Scenario: Change records from different users are not ellided
    Given users:
      | name        | status |
      | Fred Bloggs  | 1      |
      | Jane Doe     | 1      |
    Given I am logged in as "Fred Bloggs"
    And I am viewing a "discussion" with the title "Test1"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            |                  |
    When I fill in "title[0][value]" with "Test2"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test2"
    When I fill in "title[0][value]" with "Test3"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Changes diff  | Changes field    |
      | Started this discussion. | No            | Original version |
      | No                       | Yes           |                  |
      | No                       | Yes           |                  |
