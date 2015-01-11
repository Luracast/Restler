@restler
Feature: Content Negotiation - Media Type

  Scenario: One with more `q` should be selected, q = 1 when not defined
    Given that "Accept" header is set to "application/json;q=0.8,application/xml"
    When I request "/examples/_003_multiformat/bmi"
    Then the response status code should be 200
    And the response is XML
    And the type is "array"

  Scenario: One with more `q` should be selected
    Given that "Accept" header is set to "application/json;q=0.8,application/xml;q=0.4"
    When I request "/examples/_003_multiformat/bmi"
    Then the response status code should be 200
    And the response is JSON
    And the type is "array"

  Scenario: Choose first format when both are OK
    Given that "Accept" header is set to "application/xml,application/json"
    When I request "/examples/_003_multiformat/bmi"
    Then the response status code should be 200
    And the response is XML
    And the type is "array"
    And the response has a "bmi" property

  Scenario: Choose first format when both are OK
    Given that "Accept" header is set to "application/json,application/xml"
    When I request "/examples/_003_multiformat/bmi"
    Then the response status code should be 200
    And the response is JSON
    And the type is "array"
    And the response has a "bmi" property

  Scenario: JSON format by extension
    When I request "/examples/_003_multiformat/bmi.json"
    Then the response status code should be 200
    And the response is JSON

  Scenario: XML format by extension
    When I request "/examples/_003_multiformat/bmi.xml"
    Then the response status code should be 200
    And the response is XML
    And the type is "array"

  Scenario: XML format by Accept Header
    Given that "Accept" header is set to "application/xml"
    When I request "/examples/_003_multiformat/bmi"
    Then the response status code should be 200
    And the response is XML
    And the type is "array"

  Scenario: Extension should be preferred when both are specified
    Given that "Accept" header is set to "application/json"
    When I request "/examples/_003_multiformat/bmi.xml"
    Then the response status code should be 200
    And the response is XML
    And the type is "array"