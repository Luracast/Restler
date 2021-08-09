@restler
Feature: Ability to override http methods

  Scenario: Making put request from post should work
    When I request "POST tests/overrides/method?_method=PUT"
    Then the response status code should be 200
    And the response equals "put"

  Scenario: Making put request from get should fail
    When I request "GET tests/overrides/method?_method=PUT"
    Then the response status code should be 200
    And the response equals "get"

  Scenario: Making post request from put should fail
    When I request "PUT tests/overrides/method?_method=POST"
    Then the response status code should be 200
    And the response equals "put"

  Scenario: Making patch request from delete should fail
    When I request "DELETE tests/overrides/method?_method=patch"
    Then the response status code should be 200
    And the response equals "delete"

  Scenario: Making delete request from post should work
    Given that "X-HTTP-Method-Override" header is set to "DELETE"
    When I request "POST tests/overrides/method"
    Then the response status code should be 200
    And the response equals "delete"

  Scenario: Making delete request from get should fail
    Given that "X-HTTP-Method-Override" header is set to "DELETE"
    When I request "GET tests/overrides/method"
    Then the response status code should be 200
    And the response equals "get"

  Scenario: Header override should have precedence
    Given that "X-HTTP-Method-Override" header is set to "DELETE"
    When I request "POST tests/overrides/method?_method=PATCH"
    Then the response status code should be 200
    And the response equals "delete"
