@api
Feature: Mail collector
  In order for mail to actually be sent
  As a developer
  I need to make sure the test mail collector is not accidentally left enabled

  @sendmail
  Scenario: Test mail collector is not enabled
    Then the test mail collector is not enabled
    # php_mail is the OOTB default
