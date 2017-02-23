@api
Feature: List child discussions on a discussion
  In order to show discussions that are part of a discussion
  As a user viewing a discussion
  I should see child discussions and their markers.

  Background:
    Given users:
    | name         | status |
    | Fred Bloggs  | 1      |
    | Jane Doe     | 1      |

  Scenario: Visiting discussions makes them read, other people commenting makes them unread
    Given "discussion" content:
      | title  |
      | Parent |
    And "discussion" content:
      | title      | field_parents | field_participants | field_assigned | field_private | field_finished | field_help_wanted | promote |
      | Testtitle0 | Parent        | Fred Bloggs        |                | 0             | 0              | 1                 | 0       |
      | Testtitle1 | Parent        | Fred Bloggs        |                | 0             | 0              | 1                 | 1       |
      | Testtitle2 | Parent        | Fred Bloggs        | Fred Bloggs    | 0             | 0              | 1                 | 1       |
      | Testtitle3 | Parent        | Fred Bloggs        |                | 1             | 0              | 1                 | 1       |
      | Testtitle4 | Parent        | Fred Bloggs        |                | 0             | 1              | 1                 | 1       |
      | Testtitle5 | Parent        | Fred Bloggs        |                | 0             | 0              | 0                 | 1       |
      | Testtitle6 | Parent        | Fred Bloggs        |                | 0             | 0              | 0                 | 0       |
    Given I am logged in as "Jane Doe"
    When I visit "/discuss/parent"
    Then I should see "Discussion children":
      | Title field | .ahs-finished | .ahs-private | .ahs-assigned | .ahs-help-wanted | .ahs-promote |
      | Testtitle0  | No            | No           | No            | Yes              | No           |
      | Testtitle1  | No            | No           | No            | Yes              | Yes          |
      | Testtitle2  | No            | No           | Yes           | Yes              | Yes          |
      | Testtitle3  | No            | Yes          | No            | Yes              | Yes          |
      | Testtitle4  | Yes           | No           | No            | Yes              | Yes          |
      | Testtitle5  | No            | No           | No            | No               | Yes          |
      | Testtitle6  | No            | No           | No            | No               | No           |
