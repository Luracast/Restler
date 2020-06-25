@example4 @error-response
Feature: Testing Error Response

  Scenario: Calling currency format without a number
    When I request "examples/_004_error_response/currency/format"
    Then the response status code should be 400

  Scenario: Calling currency format with invalid number
    When I request "examples/_004_error_response/currency/format?number=not_a_number"
    Then the response status code should be 400

  Scenario: Calling currency format with invalid number
    When I request "examples/_004_error_response/currency/format?number=55"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "$55.00"
