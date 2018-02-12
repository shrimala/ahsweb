@api
Feature: Discussions listed on home page
  In order to surface relevant discussions
  As a user viewing the homepage
  I should see specific sets of discussions.

  Background:
    Given users:
      | name         | status |
      | Fred Bloggs  | 1      |
      | Jane Doe     | 1      |

Scenario: Per user caching
  Given "discussion" content:
    | title      | field_participants | field_assigned |
    | Testtitle4 | Jane Doe           | Jane Doe       |
    | Testtitle3 | Jane Doe           |                |
  Given I am logged in as "Fred Bloggs"
  When I visit "/"
  Then I should see no "My tasks" elements
  And I should see no "My discussions" elements
  And I should see "Active discussions":
    | Title field |
    | Testtitle3  |
    | Testtitle4  |
  Given I am logged in as "Jane Doe"
  When I visit "/"
  Then I should see "My tasks":
    | Title field |
    | Testtitle4  |
  And I should see "My discussions":
    | Title field |
    | Testtitle3  |
  And I should see no "Active discussions" elements

  Scenario: Help wanted
    # See: Help wanted, promoted, Not finished, not private, not participant, not assigned
    # See: Help wanted, promoted, Not finished, not private, participant, assigned to me
    # And still see even if not new
    # Not see: Help wanted, promoted, finished, not private, not participant, not assigned
    # Not see: Help wanted, promoted, unfinished, private, not participant, not assigned
    # Not see: Help wanted, not promoted, unfinished, not private, not participant, not assigned
    # Not see: Not help wanted, promoted, unfinished, not private, not participant, not assigned
    Given "discussion" content:
      | title      | field_participants | field_assigned | field_private | field_finished | field_help_wanted | promote |
      | Testtitle6 | Fred Bloggs        |                | 0             | 0              | 0                 | 0       |
      | Testtitle5 | Fred Bloggs        |                | 0             | 0              | 0                 | 1       |
      | Testtitle4 | Fred Bloggs        |                | 0             | 1              | 1                 | 1       |
      | Testtitle3 | Fred Bloggs        |                | 1             | 0              | 1                 | 1       |
      | Testtitle2 | Fred Bloggs        | Fred Bloggs    | 0             | 0              | 1                 | 1       |
      | Testtitle1 | Fred Bloggs        |                | 0             | 0              | 1                 | 1       |
      | Testtitle0 | Fred Bloggs        |                | 0             | 0              | 1                 | 0       |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    Then I should see "Help wanted discussions":
      | Title field |
      | Testtitle1  |
      | Testtitle2  |
      | Testtitle5  |
    And I should see "Active discussions":
      | Title field |
      | Testtitle0  |
      | Testtitle6  |
    Given I am logged in as "Fred Bloggs"
    When I visit "/"
    Then I should see "Help wanted discussions":
      | Title field |
      | Testtitle1  |
      | Testtitle2  |
      | Testtitle5  |
    And I should see "My tasks":
      | Title field |
      | Testtitle2  |
    And I should see "My discussions":
      | Title field |
      | Testtitle0  |
      | Testtitle1  |
      | Testtitle3  |
      | Testtitle4  |
      | Testtitle5  |
      | Testtitle6  |
  And I should see no "Active discussions" elements

  Scenario: My tasks
    # 1 See: Not finished, not private, participant, assigned to me
    # 2 See: Not finished, not private, participant among others, assigned to me
    # 3 See: Not finished, not private, not participant, assigned to me
    # 4 See: Not finished, not private, not participant, assigned to me and someone else
    # 12 See: Not finished, not private, not participant, assigned to me
    # 5 Not see: Not finished, not private, participant, not assigned to me
    # 6 Not see: Not finished, not private, participant, assigned to someone else
    # 7 Not see: Not finished, not private, not participant, not assigned to me
    # And if new content
    # 8 See: Finished, not private, participant, assigned to me
    # 9 See: Finished, not private, not participant, assigned to me
    # 10 See: Finished, not private, not participant, assigned to me and someone else
    Given "discussion" content:
      | title       | field_participants                | field_assigned        | field_private | field_finished |
      | Testtitle12 | Fred Bloggs                       | Jane Doe              | 1             | 0              |
      | Testtitle11 | Fred Bloggs                       |                       | 0             | 1              |
      | Testtitle10 | Fred Bloggs                       | Fred Bloggs, Jane Doe | 0             | 1              |
      | Testtitle9  | Fred Bloggs, Jane Doe             | Jane Doe              | 0             | 1              |
      | Testtitle8  | Fred Bloggs                       | Jane Doe              | 0             | 1              |
      | Testtitle7  | Fred Bloggs                       |                       | 0             | 0              |
      | Testtitle6  | Fred Bloggs                       | Fred Bloggs           | 0             | 0              |
      | Testtitle5  | Fred Bloggs, Jane Doe             |                       | 0             | 0              |
      | Testtitle4  | Fred Bloggs                       | Jane Doe, Fred Bloggs | 0             | 0              |
      | Testtitle3  | Fred Bloggs                       | Jane Doe              | 0             | 0              |
      | Testtitle2  | Fred Bloggs, Jane Doe             | Jane Doe              | 0             | 0              |
      | Testtitle1  | Jane Doe                          | Jane Doe              | 0             | 0              |
    Given I am logged in as "Jane Doe"
    When I visit "discuss/testtitle8"
    And I visit "discuss/testtitle9"
    And I visit "discuss/testtitle10"
    And I visit "/"
    # 8,9,10 should not show up because they are finished and have no new content
    Then I should see "My tasks":
      | Title field | .ahs-finished | .ahs-private | .ahs-assigned |
      | Testtitle1  | No            | No           | Yes           |
      | Testtitle2  | No            | No           | Yes           |
      | Testtitle3  | No            | No           | Yes           |
      | Testtitle4  | No            | No           | Yes           |
      | Testtitle12 | No            | Yes          | Yes           |

  Scenario: My discussions
    #1 See: Not finished, not private, participant, not assigned
    #2 See: Not finished, not private, participant with others, not assigned
    #3 See: Not finished, not private, participant, assigned to someone else
    #4 See: Not finished, private, participant, not assigned
    #5 See: Not finished, private, participant with others, not assigned
    #6 Not see: Not finished, not private, not participant, not assigned
    #7 Not see: Not finished, private, not participant, not assigned
    #8 Not see: Not finished, not private, participant, assigned to me
    #9 Not see: Not finished, not private, participant, assigned to me and someone else
    #10 Not see: Not finished, not private, not participant, assigned to me
    # And if new content
    #11 See: Finished, not private, participant, not assigned
    #12 See: Finished, not private, participant, assigned to someone else
    #13 See: Finished, private, participant, not assigned
    Given "discussion" content:
      | title       | field_participants    | field_assigned        | field_private | field_finished |
      | Testtitle13 | Jane Doe, Fred Bloggs |                       | 1             | 1              |
      | Testtitle12 | Jane Doe              | Fred Bloggs           | 0             | 1              |
      | Testtitle11 | Jane Doe              |                       | 0             | 1              |
      | Testtitle10 | Fred Bloggs           | Jane Doe              | 0             | 0              |
      | Testtitle9  | Jane Doe              | Jane Doe, Fred Bloggs | 0             | 0              |
      | Testtitle8  | Jane Doe              | Jane Doe              | 0             | 0              |
      | Testtitle7  | Fred Bloggs           |                       | 1             | 0              |
      | Testtitle6  | Fred Bloggs           |                       | 0             | 0              |
      | Testtitle5  | Fred Bloggs, Jane Doe |                       | 1             | 0              |
      | Testtitle4  | Jane Doe              |                       | 1             | 0              |
      | Testtitle3  | Jane Doe              | Fred Bloggs           | 0             | 0              |
      | Testtitle2  | Fred Bloggs, Jane Doe |                       | 0             | 0              |
      | Testtitle1  | Jane Doe              |                       | 0             | 0              |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    Then I should see "My discussions":
      | Title field | .ahs-finished | .ahs-private | .ahs-assigned |
      | Testtitle1  | No            | No           | No            |
      | Testtitle2  | No            | No           | No            |
      | Testtitle3  | No            | No           | Yes           |
      | Testtitle4  | No            | Yes          | No            |
      | Testtitle5  | No            | Yes          | No            |
      | Testtitle11 | Yes           | No           | No            |
      | Testtitle12 | Yes           | No           | Yes           |
      | Testtitle13 | Yes           | Yes          | No            |

  Scenario: Active discussions
    # See: Not finished, not private, not participant, not assigned
    # See: Not finished, not private, not participant, assigned to someone else
    # Not see: Finished, not private, participant, not assigned
    # Not see: Not finished, private, not participant, not assigned
    # Not see: Not finished, not private, participant, not assigned
    # Not see: Not finished, not private, participant with others, not assigned
    # Not see: Not finished, not private, not participant, assigned to me
    # Not see: Not finished, not private, not participant, assigned to me and someone else
    Given "discussion" content:
      | title       | field_participants    | field_assigned        | field_private | field_finished |
      | Testtitle8  | Fred Bloggs           | Jane Doe, Fred Bloggs | 0             | 0              |
      | Testtitle7  | Fred Bloggs           | Jane Doe              | 0             | 0              |
      | Testtitle6  | Fred Bloggs, Jane Doe |                       | 0             | 0              |
      | Testtitle5  | Jane Doe              |                       | 0             | 0              |
      | Testtitle4  | Fred Bloggs           |                       | 1             | 0              |
      | Testtitle3  | Fred Bloggs           |                       | 0             | 1              |
      | Testtitle2  | Fred Bloggs           | Fred Bloggs           | 0             | 0              |
      | Testtitle1  | Fred Bloggs           |                       | 0             | 0              |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    Then I should see "Active discussions":
      | Title field | .ahs-finished | .ahs-private | .ahs-assigned |
      | Testtitle1  | No            | No           | No            |
      | Testtitle2  | No            | No           | Yes           |

  Scenario: Browse
    Given "discussion" content:
      | title       | field_top_level_category | field_finished | field_private | field_participants |
      | Testtitle5  | 1                        | 0              | 1             | Fred Bloggs        |
      | Testtitle4  | 1                        | 1              | 0             | Fred Bloggs        |
      | Testtitle3  | 0                        | 0              | 0             | Fred Bloggs        |
      | Testtitle2  | 1                        | 0              | 1             | Jane Doe           |
      | Testtitle1  | 1                        | 0              | 0             | Fred Bloggs        |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    # Privacy not yet enabled for Browse top-level categories
    Then I should see "Browse categories":
      | Title field |
      | Testtitle1  |
      | Testtitle2  |
      | Testtitle5  |

  @skip @javascript
  Scenario: Search with AJAX
    Given "discussion" content:
      | title       | field_participants    | field_private |
      | Testtitle4  | Fred Bloggs           | 1             |
      | Testtitle3  | Jane Doe              | 1             |
      | Testtitle2  | Fred Bloggs, Jane Doe | 1             |
      | Testtitle1  | Fred Bloggs           | 0             |
      | Something   | Fred Bloggs           | 0             |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    And I fill in "title" with "Testtitle"
    And I press "Search"
    And I wait for AJAX to finish
    Then I should see "Browse categories":
      | Title field |
      | Testtitle1  |
      | Testtitle2  |
      | Testtitle3  |
@test
  Scenario: Search without AJAX
    Given "discussion" content:
      | title       | field_participants    | field_private | field_assigned | field_help_wanted | promote | field_finished |
      | Testtitle8  | Fred Bloggs           | 0             |                | 0                 | 0       | 1              |
      | Testtitle7  | Fred Bloggs           | 0             |                | 1                 | 1       | 0              |
      | Testtitle6  | Fred Bloggs           | 0             |                | 1                 | 0       | 0              |
      | Testtitle5  | Fred Bloggs           | 0             | Fred Bloggs    | 0                 | 0       | 0              |
      | Testtitle4  | Fred Bloggs           | 1             |                | 0                 | 0       | 0              |
      | Testtitle3  | Jane Doe              | 1             |                | 0                 | 0       | 0              |
      | Testtitle2  | Fred Bloggs, Jane Doe | 1             |                | 0                 | 0       | 0              |
      | Testtitle1  | Fred Bloggs           | 0             |                | 0                 | 0       | 0              |
      | Something   | Fred Bloggs           | 0             |                | 0                 | 0       | 0              |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    And I fill in "title" with "Testtitle"
    And I press "Search"
    Then I am visiting "/"
    And I should see "Search results":
      | Title field | .ahs-finished | .ahs-private | .ahs-assigned | .ahs-help-wanted | .ahs-promote |
      | Testtitle1  | No            | No           | No            | No               | No           |
      | Testtitle2  | No            | Yes          | No            | No               | No           |
      | Testtitle3  | No            | Yes          | No            | No               | No           |
      | Testtitle5  | No            | No           | Yes           | No               | No           |
      | Testtitle6  | No            | No           | No            | Yes              | No           |
      | Testtitle7  | No            | No           | No            | Yes              | Yes          |
      | Testtitle8  | Yes           | No           | No            | No               | No           |

  Scenario: Ancestry shown
      Given "discussion" content:
        | title       |
        | Testtitle0  |
      Given "discussion" content:
      | title       | field_parents |
      | Testtitle1  | Testtitle0    |
      | Testtitle2  | Testtitle1    |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    Then I should see "Active discussions":
      | Title field | Ancestry field          |
      | Testtitle2  | Testtitle0 / Testtitle1 |
      | Testtitle1  | Testtitle0              |
      | Testtitle0  |                         |

  Scenario: Discussions I create appear in 'My discussions' not 'Active discussions'
    Given a "discussion" with the title "Something else"
    And I am logged in as an "authenticated user"
    When I visit "/discuss/add"
    And I fill in "title[0][value]" with "Parent"
    And I press the "Save" button
    And I fill in "field_children[0][target_id]" with "Child"
    And I press the "Save" button
    And I visit "/"
    Then I should see "My discussions":
      | Title field  |
      | Child |
      | Parent       |
    And I should see "Active discussions":
      | Title field   |
      | Something else|

  Scenario: Double check exposure of finished discussions
    Given "discussion" content:
      | title       | field_participants    | field_private | field_assigned | field_help_wanted | promote | field_finished |
      | Testtitle9  | Fred Bloggs           | 1             |                | 0                 | 0       | 1              |
      | Testtitle8  | Fred Bloggs           | 0             |                | 0                 | 0       | 1              |
      | Testtitle7  | Fred Bloggs, Jane Doe | 1             |                | 0                 | 0       | 1              |
      | Testtitle6  | Fred Bloggs, Jane Doe | 0             |                | 0                 | 0       | 1              |
      | Testtitle5  | Fred Bloggs, Jane Doe | 1             | Jane Doe       | 0                 | 0       | 1              |
      | Testtitle4  | Fred Bloggs, Jane Doe | 0             | Jane Doe       | 0                 | 0       | 1              |
      | Testtitle3  | Fred Bloggs, Jane Doe | 1             |                | 1                 | 1       | 1              |
      | Testtitle2  | Fred Bloggs, Jane Doe | 0             | Jane Doe       | 1                 | 1       | 1              |
      | Testtitle1  | Fred Bloggs           | 0             |                | 1                 | 1       | 1              |
    Given I am logged in as "Jane Doe"
    When I visit "/"
    Then I should see no "Help wanted discussions" elements
    And I should see no "My tasks" elements
    And I should see "My discussions":
      | Title field |
      | Testtitle2  |
      | Testtitle3  |
      | Testtitle4  |
      | Testtitle5  |
      | Testtitle6  |
      | Testtitle7  |

  Scenario: Sorted by most recently commented, except 'Help wanted' sorted by most recently created
    Given "discussion" content:
      | title       | field_participants | field_assigned | field_private | field_finished | field_help_wanted | promote |
      | Testtitle11 | Jane Doe           |                | 0             | 0              | 0                 | 0       |
      | Testtitle10 | Jane Doe           |                | 0             | 0              | 0                 | 0       |
      | Testtitle9  | Jane Doe           |                | 0             | 0              | 0                 | 0       |
      | Testtitle8  | Fred Bloggs        |                | 0             | 0              | 0                 | 0       |
      | Testtitle7  | Fred Bloggs        |                | 0             | 0              | 0                 | 0       |
      | Testtitle6  | Fred Bloggs        |                | 0             | 0              | 0                 | 0       |
      | Testtitle5  | Fred Bloggs        | Fred Bloggs    | 0             | 0              | 0                 | 0       |
      | Testtitle4  | Fred Bloggs        | Fred Bloggs    | 0             | 0              | 0                 | 0       |
      | Testtitle3  | Fred Bloggs        | Fred Bloggs    | 0             | 0              | 0                 | 0       |
      | Testtitle0  | Jane Doe           |                | 0             | 0              | 1                 | 1       |
    Given I am logged in as "Jane Doe"
    When I visit "discuss/testtitle0"
    And I comment "some comment"
    # Create help wanted comments after a delay to force proper sort by creation date
    Given "discussion" content:
      | title       | field_participants | field_assigned | field_private | field_finished | field_help_wanted | promote |
      | Testtitle2  | Jane Doe           |                | 0             | 0              | 1                 | 1       |
    When I visit "discuss/testtitle3"
    And I comment "some comment"
    And I visit "discuss/testtitle5"
    And I comment "some comment"
    And I visit "discuss/testtitle4"
    And I comment "some comment"
    And I visit "discuss/testtitle6"
    And I comment "some comment"
    And I visit "discuss/testtitle8"
    And I comment "some comment"
    And I visit "discuss/testtitle7"
    And I comment "some comment"
    Given "discussion" content:
      | title       | field_participants | field_assigned | field_private | field_finished | field_help_wanted | promote |
      | Testtitle1  | Jane Doe           |                | 0             | 0              | 1                 | 1       |
    When I visit "discuss/testtitle1"
    And I comment "some comment"
    When I visit "discuss/testtitle9"
    And I comment "some comment"
    And I visit "discuss/testtitle11"
    And I comment "some comment"
    And I visit "discuss/testtitle10"
    And I comment "some comment"
    When I visit "discuss/testtitle2"
    And I comment "some comment"
    Given I am logged in as "Fred Bloggs"
    When I visit "/"
    Then I should see "Help wanted discussions":
      | Title field |
      | Testtitle1  |
      | Testtitle2  |
      | Testtitle0  |
    And I should see "My tasks":
      | Title field |
      | Testtitle4  |
      | Testtitle5  |
      | Testtitle3  |
    And I should see "My discussions":
      | Title field |
      | Testtitle7  |
      | Testtitle8  |
      | Testtitle6  |
    And I should see "Active discussions":
      | Title field |
      | Testtitle10  |
      | Testtitle11  |
      | Testtitle9  |
