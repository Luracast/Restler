@restler
Feature: Content Negotiation - Charset

  Scenario: Verify Charset support
    Given that "Accept-Charset" header is set to "iso-8859-1"
    When I request "/examples/_002_minimal/math/add"
    Then the response status code should be 200
    And the response charset is "iso-8859-1"

  Scenario: Unsuported Charset should return 406
    Given that "Accept-Charset" header is set to "iso-8859-5"
    When I request "/examples/_002_minimal/math/add"
    Then the response status code should be 406