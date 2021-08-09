@example13 @html
Feature: Testing Html

  Scenario: Getting Html response
    When I request "examples/_013_html/tasks.html"
    Then the response status code should be 200
    And the response is HTML

  Scenario: Getting Json response
    When I request "examples/_013_html/tasks.json"
    Then the response status code should be 200
    And the response is JSON

  Scenario: Getting Html response for a tag
    When I request "examples/_013_html/tasks/1.html"
    Then the response status code should be 200
    And the response is HTML

  Scenario: Getting Json response for a tag
    When I request "examples/_013_html/tasks/2.json"
    Then the response status code should be 200
    And the response is JSON

  Scenario: Deleting a task
    Given that I want to delete a "Task"
    And his "id" is 1
    When I request "examples/_013_html/tasks/{id}.json"
    Then the response status code should be 200
    And the response should be JSON
    And the response has an "id" property

  Scenario: Getting Html response for a missing tag
    When I request "examples/_013_html/tasks/1.html"
    Then the response status code should be 404
    And the response is HTML

  Scenario: Getting Json response for a deleted tag
    When I request "examples/_013_html/tasks/1.json"
    Then the response status code should be 404
    And the response is JSON
    And the response has a "error" property


