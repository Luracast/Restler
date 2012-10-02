@restler
Feature: HTTP Status Codes

  Scenario: I should be able to suppress status code 404
    When I request "/examples/_001_helloworld?suppress_response_codes=true"
    Then the response status code should be 200