@example12 @vendormime
Feature: Testing Vendor Media Type Versioning

  Scenario: Access version 1 by vendor media type
    Given that "Accept" header is set to "application/vnd.SomeVendor-v1+json"
    When I request "examples/_012_vendor_mime/bmi?height=190"
    Then the response status code should be 200
    And the response "Content-Type" header should be "application/vnd.SomeVendor-v1+json; charset=utf-8"
    And the response is JSON
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Normal weight"
    And the "metric.height" property equals "190 centimeters"

  Scenario: Access version 2 by vendor media type and passing invalid argument
    Given that "Accept" header is set to "application/vnd.SomeVendor-v2+json"
    When I request "examples/_012_vendor_mime/bmi?height=190"
    Then the response status code should be 400
    And the response "Content-Type" header should be "application/vnd.SomeVendor-v2+json; charset=utf-8"
    And the response is JSON
    And the type is "array"
    And the "error.message" property equals "invalid height unit"

  Scenario: Access version 2 by vendor media type
    Given that "Accept" header is set to "application/vnd.SomeVendor-v2+json"
    When I request "examples/_012_vendor_mime/bmi?height=190cm"
    Then the response status code should be 200
    And the response "Content-Type" header should be "application/vnd.SomeVendor-v2+json; charset=utf-8"
    And the response is JSON
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Normal weight"
    And the "metric.height" property equals "190 centimeters"