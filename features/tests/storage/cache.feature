@storage @cache
Feature: Storing and retrieving value from cache

  Scenario: no value is set
    When I request "tests/storage/cache"
    Then the response status code should be 200
    And the response is JSON
    And the "exists" property equals false
    And the "value" property equals null

  Scenario: storing value
    Given that I send {"value":"All is well"}
    And the request is sent as JSON
    When I request "tests/storage/cache"
    Then the response status code should be 200
    And the type is "bool"
    And the value equals true

  Scenario: retrieving value
    When I request "tests/storage/cache"
    Then the response status code should be 200
    And the response is JSON
    And the "exists" property equals true
    And the "value" property equals "All is well"
