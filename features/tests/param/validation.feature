@param @type
Feature: Validation

  Scenario Outline: Valid Password
    Given that I send {"password":<password>}
    And the request is sent as JSON
    When I request "/tests/param/validation/pattern"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <password>

  Examples:
    | password |
    | "1a"     |
    | "b2"     |
    | "some1"  |

  Scenario Outline: Invalid Password
    Given that I send {"password":<password>}
    And the request is sent as JSON
    When I request "/tests/param/validation/pattern"
    Then the response status code should be 400
    And the response is JSON
    And the type is "string"
    And the response contains "Bad Request: Strong password with at least one alpha and one numeric character is required"

  Examples:
    | password   |
    | "arul"     |
    | "12345678" |
    | "ONEtwo"   |