@example2 @minimal
Feature: Testing Minimal Example

  Scenario: Add using Default Values
    When I request "examples/_002_minimal/math/add"
    Then the response status code should be 200
    And the response is JSON
    And the type is "int"
    And the value equals 2

  Scenario: Add 5 and 10
    Given that "n1" is set to "5"
    And "n2" is set to "10"
    When I request "examples/_002_minimal/math/add{?n1,n2}"
    Then the response status code should be 200
    And the response is JSON
    And the type is "int"
    And the value equals 15

  Scenario: Verify Validation
    Given that "n1" is set to "NaN"
    When I request "examples/_002_minimal/math/add{?n1,n2}"
    Then the response status code should be 400
    And the response is JSON
    And the response has a "error" property

  Scenario: Multiply
    Given that "n1" is set to "10"
    And "n2" is set to "5"
    When I request "examples/_002_minimal/math/multiply/{n1}/{n2}"
    And the response is JSON
    And the response has a "result" property
    And the "result" property equals 50

  Scenario: Multiply without value
    When I request "examples/_002_minimal/math/multiply"
    Then the response status code should be 404
    And the response is JSON
    And the type is "array"
    And the response has a "error" property

  Scenario: Verify Validation for multiplication
    Given that "n1" is set to "NaN"
    And "n2" is set to "5"
    When I request "examples/_002_minimal/math/multiply/{n1}/{n2}"
    Then the response status code should be 400
    And the response is JSON
    And the response has a "error" property
