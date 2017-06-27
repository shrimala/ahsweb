@api
Feature: Discussion comments can be edited by their owner
  In order to keep discussions tidy
  As a user who has made a comment
  I need to be able to edit or delete my comment

  Scenario: Normal users Can edit own comment, not someone else's
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
    # No delete link visible for comment or discussion
    And I should not see "Delete"
    And I should not see "Reply"
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/test"
    When I comment "Jane's comment"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  |
      | Started this discussion. | No                 |
      | Fred's comment           | No                 |
      | Jane's comment           | Yes                |
    Given I am logged in as "Fred Bloggs"
    When I visit "/discuss/test"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  |
      | Started this discussion. | No                 |
      | Fred's comment           | Yes                |
      | Jane's comment           | No                 |

  Scenario: Comments can be edited and deleted
    Given I am logged in as an "authenticated user"
    And a "discussion" with the title "test"
    When I comment "My comment"
    When I click on the ".field--name-field-comments-with-changes article:last-of-type ul.ahs-edit-btn a" element
    And I fill in "comment_body[0][value]" with "My new comment"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  |
      | Started this discussion. | No                 |
      | My new comment           | Yes                |
    When I click on the ".field--name-field-comments-with-changes article:last-of-type ul.ahs-edit-btn a" element
    And I fill in "comment_body[0][value]" with "My new comment"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  |
      | Started this discussion. | No                 |
      | My new comment           | Yes                |

  Scenario: Discussion admin can edit and delete others comments
    Given I am logged in as an "authenticated user"
    And a "discussion" with the title "test"
    When I comment "User comment"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link |
      | Started this discussion. | No                |
      | User comment             | Yes               |
    Given I am logged in as a "Discussion admin"
    And I visit "/discuss/test"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link |
      | Started this discussion. | No                |
      | User comment             | Yes               |
    And I should not see "Reply"
    When I click on the ".field--name-field-comments-with-changes article:last-of-type ul.ahs-edit-btn a" element
    And I fill in "comment_body[0][value]" with "Admin comment"
    And I press "Save"
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link  |
      | Started this discussion. | No                 |
      | Admin comment            | Yes                |
    When I click on the ".field--name-field-comments-with-changes article:last-of-type ul.ahs-edit-btn a" element
    And I follow "Delete"
    And I press the "Delete" button
    Then I should see "Discussion comments":
      | Comment body field       | Comment edit link |
      | Started this discussion. | No                |