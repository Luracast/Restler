@param @min @max
Feature: Minimum and Maximum

  Scenario Outline: Int
    When I request "tests/param/minmax/int/<number>"
    Then the response status code should be 200
    And the response is JSON
    And the type is "int"
    And the value equals <number>

    Examples:
      | number |
      |   2    |
      |   3    |
      |   4    |
      |   5    |

  Scenario: Int lower than the minimum
    When I request "tests/param/minmax/int/1"
    Then the response status code should be 400
    And the response is JSON

  Scenario: Int higher than maximum
    When I request "tests/param/minmax/int/6"
    Then the response status code should be 400
    And the response is JSON

  Scenario: String
    When I request "tests/param/minmax/string/me"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "me"

  Scenario: Short String
    When I request "tests/param/minmax/string/i"
    Then the response status code should be 400
    And the response is JSON

  Scenario: Lengthy String
    When I request "tests/param/minmax/string/arulkumaran"
    Then the response status code should be 400
    And the response is JSON

  Scenario Outline: Array
    Given that I send <data>
    And the request is sent as JSON
    When I request "tests/param/minmax/array"
    Then the response status code should be 200
    And the response is JSON

    Examples:
       |  data       |
       | [1,2]       |
       | [1,2,3]     |
       | [1,2,3,4]   |
       | [1,2,3,4,5] |

  Scenario Outline: Array out of range
    Given that I send <data>
    And the request is sent as JSON
    When I request "tests/param/minmax/array"
    Then the response status code should be 400
    And the response is JSON

    Examples:
       |  data             |
       | []                |
       | [1]               |
       | [1,2,3,4,5,6]     |
       | [1,2,3,4,5,6,7]   |
       | [1,2,3,4,5,6,7,8] |
