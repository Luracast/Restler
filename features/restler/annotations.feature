@restler @annotation
Feature: Annotations

  Scenario: @class should set the property correctly
    When I request "/examples/_010_access_control/admin"
    Then the response status code should be 401

  Scenario: @cache should reflect in the header
    When I request "/examples/_009_rate_limiting/authors"
    Then the response "Cache-Control" header should be "public, max-age=30, max-stale=3000, must-revalidate"

  Scenario: @expires should set correct time difference between Date and Expires headers
    When I request "/examples/_009_rate_limiting/authors"
    Then the response "Expires" header should be Date+30 seconds

  Scenario: @status should set respective status code
    Given that I want to make a new "Author"
    And his "name" is "Superman"
    And his "email" is "super@man.world"
    When I request "/examples/_009_rate_limiting/authors"
    Then the response status code should be 201

  Scenario: @throttle should correctly delay the response
    When I request "/examples/_009_rate_limiting/authors"
    Then the response time should at least be 200 milliseconds
