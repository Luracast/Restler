@example9 @crud
Feature: Testing Rate Limiting Example

  Scenario: Failing to delete missing Author with JSON
    Given that I want to delete an "Author"
    And his "id" is 2000
    When I request "/examples/_009_rate_limiting/authors/{id}?api_key=r3rocks"
    Then the response status code should be 404