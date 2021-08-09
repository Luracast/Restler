@restler
Feature: Testing ability to set properties via docblock comments

  Scenario: Transforming response by specifying a separator character
    When I request "tests/overrides/property/transform"
    Then the response status code should be 200
    And the response should be JSON
    And the response has a "author" property
    And the "author.name" property equals "Arul"
    And the "author.email" property equals "arul@luracast.com"