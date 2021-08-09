@example3 @multiformat
Feature: Testing Multi-format Example

  Scenario: Default format, when not specified
    When I request "examples/_003_multiformat/bmi"
    Then the response status code should be 200
    And the response is JSON
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Obesity"

  Scenario: Use XML format when specified as extension
    When I request "examples/_003_multiformat/bmi.xml"
    Then the response status code should be 200
    And the response is XML
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Obesity"

  Scenario: Use XML format through content negotiation
    When I accept "text/html; q=1.0, application/xml; q=0.8, application/json; q=0.5, */*; q=0.1"
    And accept language "de; q=1.0, en; q=0.5"
    And request "examples/_003_multiformat/bmi"
    Then the response status code should be 200
    And the response is XML
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Obesity"

  Scenario: Use XML format through content negotiation even with reverse order
    When I accept "*/*; q=0.1, application/json; q=0.5, application/xml; q=0.8, text/html; q=1.0"
    And accept language "de; q=1.0, en; q=0.5"
    And request "examples/_003_multiformat/bmi"
    Then the response status code should be 200
    And the response is XML
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Obesity"

  Scenario: Correct weight and height should yield 'Normal weight' as result
    Given that "height" is set to 180
    And "weight" is set to 80
    When I request "examples/_003_multiformat/bmi.xml{?height,weight}"
    Then the response status code should be 200
    And the "message" property equals "Normal weight"
