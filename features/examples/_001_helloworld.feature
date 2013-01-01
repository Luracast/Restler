@example1 @helloworld
Feature: Testing Helloworld Example

  Scenario: Saying Hello world
    When I request "/examples/_001_helloworld/say/hello"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "Hello world!"

  Scenario: Saying Hello Restler
    Given that "to" is set to "Restler"
    When I request "/examples/_001_helloworld/say/hello{?to}"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "Hello Restler!"

  Scenario: Saying
    When I request "/examples/_001_helloworld/say"
    Then the response status code should be 404
    And the response is JSON
    And the type is "array"

  Scenario: Saying Hi
    When I request "/examples/_001_helloworld/say/hi"
    Then the response status code should be 404
    And the response is JSON
    And the type is "array"

  Scenario: Saying Hi Arul
    Given that "to" is set to "Arul"
    When I request "/examples/_001_helloworld/say/hi/{to}"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "Hi Arul!"