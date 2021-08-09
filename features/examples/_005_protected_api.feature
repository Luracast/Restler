@example5 @protected-api
Feature: Testing Protected Api

  Scenario: Calling restricted api without a key
    When I request "examples/_005_protected_api/restricted"
    Then the response status code should be 401

  Scenario: Calling restricted api with invalid key
    When I request "examples/_005_protected_api/restricted?key=not-valid"
    Then the response status code should be 401

  Scenario: Calling restricted api with valid key
    When I request "examples/_005_protected_api/restricted?key=rEsTlEr4"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected method"

  Scenario: Calling restricted api class with valid key
    When I request "examples/_005_protected_api/secured?key=rEsTlEr4"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected class"