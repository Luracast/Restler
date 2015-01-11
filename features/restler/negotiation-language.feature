@restler
Feature: Content Negotiation - Language

  Scenario: Supported Language should be used when specified
    Given that "Accept-Language" header is set to "en-US"
    When I request "/examples/_002_minimal/math/add"
    Then the response status code should be 200
    And the response language is "en-US"

  Scenario: Unsupported Language should be ignored
    Given that "Accept-Language" header is set to "fr"
    When I request "/examples/_002_minimal/math/add"
    Then the response status code should be 200
    And the response language is "en"