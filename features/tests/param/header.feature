@param @header
Feature: Sending parameter from header

  Scenario Outline: I should be able to read a parameter from header
    Given that "api_key" header is set to <api_key>
    When I request "tests/param/header"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <api_key>

    Examples:
      | api_key                                |
      | "4b972cba-f138-11e7-8c3f-9a214cf093ae" |
      | "4b973020-f138-11e7-8c3f-9a214cf093ae" |
      | "4B973200-F138-11E7-8C3F-9A214CF093AE" |
      | "4b9733b8-f138-11e7-8c3f-9a214cf093ae" |

  Scenario: query parameter should not be considered for header parameter
    When I request "tests/param/header?api_key=4b972cba-f138"
    Then the response status code should be 400

  Scenario: body parameter should not be considered for header parameter
    Given that I send {"api_key":"4b972cba-f138"}
    When I request "tests/param/header"
    Then the response status code should be 400

