@api @skip
Feature: Event tabs
  In order to navigate event information
  As any user with access to events
  I need to see the correct tabs

  Background:
    Given I am logged in as an "authenticated user"
    And a session with the title "Session1"
    And event content:
      | title           | field_datetime      |
      | Event1        | 2005-12-30 00:00:00 |
    When I visit "events/2005/12/30/event1"

  Scenario: Event tabs on event overview page
    Then I should see "Local tabs":
      | contains          | .active |
      | Overview          | Yes     |
      | Details           | No      |
      | Online            | No      |
      | Manage online     | No      |
      | Manage recordings | No      |
      | Edit event        | No      |

  Scenario: Event tabs not on session page
    When I visit "admin/content"
    And I click "Session1"
    Then I should not see "Overview"
    And I should not see "Details"
    And I should not see "Online"
    And I should not see "Manage online"
    And I should not see "Manage recordings"

  Scenario: Event tabs on Details page
    When I click "Details"
    Then I should see "Local tabs":
      | contains          | .active |
      | Overview          | No      |
      | Details           | Yes     |
      | Online            | No      |
      | Manage online     | No      |
      | Manage recordings | No      |
      | Edit event        | No      |

  Scenario: Event tabs on Online page
    When I click "Online"
    Then I should see "Local tabs":
      | contains          | .active |
      | Overview          | No      |
      | Details           | No      |
      | Online            | Yes     |
      | Manage online     | No      |
      | Manage recordings | No      |
      | Edit event        | No      |

  Scenario: Event tabs on Manage online page
    When I click "Manage online"
    Then I should see "Admin local tabs":
      | contains          | .is-active |
      | Overview          | No      |
      | Details           | No      |
      | Online            | No      |
      | Manage online     | Yes     |
      | Manage recordings | No      |
      | Edit event        | No      |

  Scenario: Event tabs on Manage recordings page
    When I click "Manage recordings"
    Then I should see "Admin local tabs":
      | contains          | .is-active |
      | Overview          | No      |
      | Details           | No      |
      | Online            | No      |
      | Manage online     | No      |
      | Manage recordings | Yes     |
      | Edit event        | No      |

  Scenario: Event tabs on Edit event page
    When I click "Edit event"
    Then I should see "Admin local tabs":
      | contains          | .is-active |
      | Overview          | No      |
      | Details           | No      |
      | Online            | No      |
      | Manage online     | No      |
      | Manage recordings | No      |
      | Edit event        | Yes     |