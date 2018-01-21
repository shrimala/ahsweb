@api
Feature: Site settings
  In order for everything to work
  As a site builder
  I need to make sure the basic site settings are correct

  Scenario: Test mail collector is not accidentally left enabled
    Then the test mail collector is not enabled
    # php_mail is the OOTB default

  Scenario: Site timezone is set correctly
    Then the site timezone is "Europe/London"
    # PHPstorm does't recognise the step because of the slash, but it works fine
