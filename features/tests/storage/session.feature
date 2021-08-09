@storage @session
Feature: Storing and retrieving value from session

  Background: we are starting anew

  Scenario: no value is set
    When I request "tests/storage/session"
    Then the response status code should be 200
    And the response is JSON
    And the "exists" property equals false
    And the "value" property equals null
    # And echo last response

  Scenario: storing value
    Given that I send {"value":"Knowledge is POWER"}
    And the request is sent as JSON
    When I request "tests/storage/session"
    Then the response status code should be 200
    And the type is "bool"
    And the value equals true

  Scenario: retrieving value
    When I request "tests/storage/session"
    Then the response status code should be 200
    And the response is JSON
    And the "exists" property equals true
    And the "value" property equals "Knowledge is POWER"
