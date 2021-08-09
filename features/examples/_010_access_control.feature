@example10 @access-control
Feature: Testing Access Control

  Scenario: Access public api without a key
    When I request "examples/_010_access_control/all"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "public api, all are welcome"

  Scenario: Access public api with user key
    When I request "examples/_010_access_control/all?api_key=123"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "public api, all are welcome"

  Scenario: Access public api with admin key
    When I request "examples/_010_access_control/all?api_key=789"
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
    When I request "examples/_010_access_control/user?api_key=123"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected api, only user and admin can access"

  Scenario: Access user api with admin key
    When I request "examples/_010_access_control/user?api_key=789"
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
    When I request "examples/_010_access_control/admin?api_key=456"
    Then the response status code should be 401
    And the response is JSON
    And the "error.message" property equals "Unauthorized"

  Scenario: Access admin api with admin key
    When I request "examples/_010_access_control/admin?api_key=789"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected api, only admin can access"

  Scenario: Access document with owner key
    When I request "examples/_010_access_control/documents/1?api_key=123"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected document, only user who owns it and admin can access"

  Scenario: Access document with admin key
    When I request "examples/_010_access_control/documents/1?api_key=789"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected document, only user who owns it and admin can access"

  Scenario: Access document with non owner key
    When I request "examples/_010_access_control/documents/1?api_key=456"
    Then the response status code should be 403
    And the response is JSON
    And the "error.message" property equals "permission denied."

  Scenario: Access document with invalid key
    When I request "examples/_010_access_control/documents/2?api_key=678"
    Then the response status code should be 401
    And the response is JSON
    And the "error.message" property equals "Unauthorized"

  Scenario: Access non existent document
    When I request "examples/_010_access_control/documents/5?api_key=456"
    Then the response status code should be 404
    And the response is JSON
    And the "error.message" property equals "document does not exist."

  Scenario Outline: Access documents with different keys
    When I request "examples/_010_access_control/documents?api_key=<key>"
    Then the response status code should be 200
    And the response is JSON
    And the response equals <expected>

    Examples:
      | key | expected  |
      | 123 | [1,3]     |
      | 456 | [2]       |
      | 789 | [1,2,3,4] |


