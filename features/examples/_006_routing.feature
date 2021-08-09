@example6 @routing
Feature: Testing Routing Example

  Scenario: Testing So Many Ways
    Given that "p2" is set to 2
    When I request "examples/_006_routing/api/somanyways/1{?p2}"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::soManyWays()"

  Scenario: Testing So Many Ways with two params
    When I request "examples/_006_routing/api/somanyways/1/2"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::soManyWays()"

  Scenario: Testing So Many Ways with three params
    When I request "examples/_006_routing/api/somanyways/1/2/3"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::soManyWays()"

  Scenario: Testing So Many Ways with more params
    When I request "examples/_006_routing/api/somanyways/1/2/3/4"
    Then the response status code should be 404
    And the response is JSON

  Scenario: Ignoring required parameter should throw 400
    When I request "examples/_006_routing/api/what/ever/you/want"
    Then the response status code should be 400
    And the response is JSON

  Scenario: Testing Wildcard route with 7 parameters
    When I request "examples/_006_routing/api/all/1/2/3/4/5/6/7"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::allIsMine(1, 2, 3, 4, 5, 6, 7)"

  Scenario: Testing Wildcard route with 0 parameters
    When I request "examples/_006_routing/api/all"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::allIsMine()"
