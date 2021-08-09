@example15 @oauth
Feature: Testing OAuth Server

  Scenario: Getting Html response
    When I request "examples/_015_oauth2_server/authorize?response_type=code&client_id=demoapp&redirect_uri=http%3A%2F%2Flocalhost%3A8080%2Fdev%2Fexamples%2F_014_oauth2_client%2Fauthorized&state=dbb8c604ae571e6e2a54144b1a74d8243c353f94a3e9bab3f673b258d87073b8"
    Then the response status code should be 200
    And the response is HTML
