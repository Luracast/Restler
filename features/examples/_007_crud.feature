@example7 @crud
Feature: Testing CRUD Example

  Scenario: Creating new Author by POSTing vars
    Given that I want to make a new "Author"
    And his "name" is "Chris"
    And his "email" is "chris@world.com"
    When I request "/examples/_007_crud/authors"
    Then the response status code should be 200
    And the response should be JSON
    And the response has a "id" property

  Scenario: Creating new Author with JSON
    Given that I want to make a new "Author"
    And his "name" is "Chris"
    And his "email" is "chris@world.com"
    And the request is sent as JSON
    When I request "/examples/_007_crud/authors"
    Then the response status code should be 200
    And the response should be JSON
    And the response has a "id" property

  Scenario: Updating Author with JSON
    Given that I want to update "Author"
    And his "name" is "Jac"
    And his "email" is "jac@wright.com"
    And his "id" is 1
    And the request is sent as JSON
    When I request "/examples/_007_crud/authors/{id}"
    Then the response status code should be 200
    And the response should be JSON
    And the response has a "id" property

  Scenario: Given url is valid for other http method(s)
    Given that I want to update "Author"
    And his "name" is "Jac"
    And his "email" is "jac@wright.com"
    And his "id" is 1
    And the request is sent as JSON
    When I request "/examples/_007_crud/authors"
    Then the response status code should be 405

  Scenario: Deleting Author
    Given that I want to delete an "Author"
    And his "id" is 1
    When I request "/examples/_007_crud/authors/{id}"
    Then the response status code should be 200
    And the response should be JSON
    And the response has an "id" property