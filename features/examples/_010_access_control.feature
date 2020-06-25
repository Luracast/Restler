@example10 @access-control
Feature: Testing Access Control

  Scenario: Access public api without a key
    When I request "examples/_010_access_control/all"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "public api, all are welcome"

  Scenario: Access public api with user key
    When I request "examples/_010_access_control/all?api_key=12345"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "public api, all are welcome"

  Scenario: Access public api with admin key
    When I request "examples/_010_access_control/all?api_key=67890"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "public api, all are welcome"

  Scenario: Access public api with wrong key
    When I request "examples/_010_access_control/all?api_key=00000"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "public api, all are welcome"

  Scenario: Access user api without a key
    When I request "examples/_010_access_control/user"
    Then the response status code should be 401
    And the response is JSON
    And the "error.message" property equals "Unauthorized"

  Scenario: Access user api with user key
    When I request "examples/_010_access_control/user?api_key=12345"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected api, only user and admin can access"

  Scenario: Access user api with admin key
    When I request "examples/_010_access_control/user?api_key=67890"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected api, only user and admin can access"

  Scenario: Access admin api without a key
    When I request "examples/_010_access_control/admin"
    Then the response status code should be 401
    And the response is JSON
    And the "error.message" property equals "Unauthorized"

  Scenario: Access admin api with user key
    When I request "examples/_010_access_control/admin?api_key=12345"
    Then the response status code should be 401
    And the response is JSON
    And the "error.message" property equals "Unauthorized"

  Scenario: Access admin api with admin key
    When I request "examples/_010_access_control/admin?api_key=67890"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected api, only admin can access"
