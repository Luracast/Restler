@example9 @rate-limit
Feature: Testing Rate Limiting Example

  Scenario: Creating new Author by POSTing vars
    Given that I want to make a new "Author"
    And his "name" is "Chris"
    And his "email" is "chris@world.com"
    When I request "examples/_009_rate_limiting/authors"
    Then the response status code should be 201
    And the response should be JSON
    And the response has a "id" property

  Scenario: Creating new Author with JSON
    Given that I want to make a new "Author"
    And his "name" is "Chris"
    And his "email" is "chris@world.com"
    And the request is sent as JSON
    When I request "examples/_009_rate_limiting/authors"
    Then the response status code should be 201
    And the response should be JSON
    And the response has a "id" property

  Scenario: Updating Author with JSON without Authentication
    Given that I want to update "Author"
    And his "name" is "Jac"
    And his "email" is "jac@wright.com"
    And his "id" is 1
    And the request is sent as JSON
    When I request "examples/_009_rate_limiting/authors/{id}"
    Then the response status code should be 401
    And the response "WWW-Authenticate" header should be 'Query name="api_key"'
    And the response should be JSON
    And the "error.message" property equals "Unauthorized"

  Scenario: Updating Author with JSON with Authentication
    Given that I want to update "Author"
    And his "name" is "Jac"
    And his "email" is "jac@wright.com"
    And his "id" is 1
    And the request is sent as JSON
    When I request "examples/_009_rate_limiting/authors/{id}?api_key=r4rocks"
    Then the response status code should be 200
    And the response should be JSON
    And the "name" property equals "Jac"

  Scenario: Given url is valid for other http method(s)
    Given that I want to update "Author"
    And his "name" is "Jac"
    And his "email" is "jac@wright.com"
    And his "id" is 1
    And the request is sent as JSON
    When I request "examples/_009_rate_limiting/authors"
    Then the response status code should be 405
    And the response "Allow" header should be "GET, POST"

  Scenario: Deleting Author
    Given that I want to delete an "Author"
    And his "id" is 1
    When I request "examples/_009_rate_limiting/authors/{id}?api_key=r4rocks"
    Then the response status code should be 200
    And the response should be JSON
    And the response has an "id" property

  Scenario: Deleting with invalid author id
    Given that I want to delete an "Author"
    And his "id" is 1
    When I request "examples/_009_rate_limiting/authors/{id}?api_key=r4rocks"
    Then the response status code should be 404
    And the response should be JSON
