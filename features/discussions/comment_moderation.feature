@api
Feature: Discussion comments can be edited by their owner
  In order to keep discussions tidy
  As a user who has made a comment
  I need to be able to edit or delete my comment

  Scenario: For normal users edit links appear for own comment, not someone else's
    Given users:
      | name        | status |
      | Fred Bloggs  | 1      |
      | Jane Doe     | 1      |
    And I am logged in as "Fred Bloggs"
    And a "discussion" with the title "test"
    When I comment "Fred's comment"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link |
      | Started this discussion. | No                |
      | Fred's comment           | Yes               |
    # No link texts visible for comment or discussion
    And I should not see "Delete"
    And I should not see "Reply"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    When I comment "Jane's comment"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  | Comment delete link  |
      | Started this discussion. | No                 | No                   |
      | Fred's comment           | No                 | No                   |
      | Jane's comment           | Yes                | No                   |
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/test"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  | Comment delete link  |
      | Started this discussion. | No                 | No                   |
      | Fred's comment           | Yes                | No                   |
      | Jane's comment           | No                 | No                   |

  Scenario: Own comments can be edited by normal users
    Given I am logged in as an "authenticated user"
    And a "discussion" with the title "test"
    When I comment "My comment"
    When I click on the ".field--name-field-comments-with-changes article:last-of-type ul.links .comment-edit a" element
    And I fill in "comment_body[0][value]" with "My 2nd comment"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  | Comment delete link  |
      | Started this discussion. | No                 | No                   |
      | My 2nd comment           | Yes                | No                   |
    When I click on the ".field--name-field-comments-with-changes article:last-of-type ul.links .comment-edit a" element
    And I fill in "comment_body[0][value]" with "My 3rd comment"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  | Comment delete link  |
      | Started this discussion. | No                 | No                   |
      | My 3rd comment           | Yes                | No                   |

  Scenario: Discussion admin can edit and delete others comments
    Given I am logged in as an "authenticated user"
    And a "discussion" with the title "test"
    When I comment "User comment"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link | Comment delete link  |
      | Started this discussion. | No                | No                   |
      | User comment             | Yes               | No                   |
    Given I am logged in as a "Discussion admin"
    And I visit "/discuss/test"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link | Comment delete link  |
      | Started this discussion. | Yes               | Yes                  |
      | User comment             | Yes               | Yes                  |
    And I should not see "Reply"
    When I click on the ".field--name-field-comments-with-changes article:last-of-type ul.links .comment-edit a" element
    And I fill in "comment_body[0][value]" with "Admin comment"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  | Comment delete link  |
      | Started this discussion. | Yes                | Yes                  |
      | Admin comment            | Yes                | Yes                  |
    When I click on the ".field--name-field-comments-with-changes article:last-of-type ul.links .comment-delete  a" element
    And I press the "Delete" button
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link | Comment delete link  |
      | Started this discussion. | Yes               | Yes                  |