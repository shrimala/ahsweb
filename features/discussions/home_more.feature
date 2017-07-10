# Behaviour is different without javascript, so tests need js

#@api
#Feature: Discussions listed on home page
#  In order to surface relevant discussions
#  As a user viewing the homepage
#  I should see specific sets of discussions.
#
#  Scenario: My discussions
#    Given users:
#      | name         | status |
#      | Fred Bloggs  | 1      |
#    Given "discussion" content:
#      | title  | field_participants    |
#      | test21  | Fred Bloggs           |
#      | test20  | Fred Bloggs           |
#      | test19  | Fred Bloggs           |
#      | test18  | Fred Bloggs           |
#      | test17  | Fred Bloggs           |
#      | test16  | Fred Bloggs           |
#      | test15  | Fred Bloggs           |
#      | test14  | Fred Bloggs           |
#      | test13  | Fred Bloggs           |
#      | test12  | Fred Bloggs           |
#      | test11  | Fred Bloggs           |
#      | test10  | Fred Bloggs           |
#      | test9  | Fred Bloggs           |
#      | test8  | Fred Bloggs           |
#      | test7  | Fred Bloggs           |
#      | test6  | Fred Bloggs           |
#      | test5  | Fred Bloggs           |
#      | test4  | Fred Bloggs           |
#      | test3  | Fred Bloggs           |
#      | test2  | Fred Bloggs           |
#      | test1  | Fred Bloggs           |
#    Then I break
#    Given I am logged in as "Fred Bloggs"
#    When I visit "/"
#    Then I should see "My discussions":
#      | Title field |
#      | test1       |
#      | test2       |
#      | test3       |
#      | test4       |
#      | test5       |
#      | test6       |
#      | test7       |
#      | test8       |
#      | test9       |
#      | test10      |
#    When I press "More"
#    Then I should see "My discussions":
#      | Title field |
#      | test1       |
#      | test2       |
#      | test3       |
#      | test4       |
#      | test5       |
#      | test6       |
#      | test7       |
#      | test8       |
#      | test9       |
#      | test10      |
#      | test11       |
#      | test12       |
#      | test13       |
#      | test14       |
#      | test15       |
#      | test16       |
#      | test17       |
#      | test18       |
#      | test19       |
#      | test20       |
