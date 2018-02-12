@ddg
Feature: a first attempt to test behat
     In order to see the good working of @tag website
     As a guest user
     I need to see the home page of google.fr

Scenario: check the status code
      Given  I am on "/"
      Then   the response status code should be 200

Scenario: check the visibility of the research button
     Given  I am on "/"
     When   I fill in "search_form_input_homepage" with "Google"
     And    I press "search_button_homepage"
     Then   I should see "Google"

Scenario: check the title
    Given  I am on "/"
    Then the element with xpath ".//*[@id='content_homepage']/div/div[3]/div/div/span/a" should contain "En savoir plus"
