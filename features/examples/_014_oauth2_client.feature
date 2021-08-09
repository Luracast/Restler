@example14 @oauth
Feature: Testing OAuth Client

  Scenario: Getting Html response
    When I request "examples/_014_oauth2_client/index.html"
    Then the response status code should be 200
    And the response is HTML
