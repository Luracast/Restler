@param @min @max @fix
Feature: Minimum and Maximum with Fix

  Scenario Outline: Int
    When I request "tests/param/minmaxfix/int/<number>"
    Then the response status code should be 200
    And the response is JSON
    And the type is "int"
    And the response equals <expected>

  Examples:
    | number | expected |
    | 0      | 2        |
    | 1      | 2        |
    | 2      | 2        |
    | 3      | 3        |
    | 4      | 4        |
    | 5      | 5        |
    | 6      | 5        |
    | 7      | 5        |

  Scenario Outline: String
    Given that I send {"string":<string>}
    And the request is sent as JSON
    When I request "tests/param/minmaxfix/string"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <expected>

  Examples:
    | string    | expected |
    | "a"       | "aa"     |
    | "ab"      | "ab"     |
    | "abc"     | "abc"    |
    | "abcd"    | "abcd"   |
    | "abcde"   | "abcde"  |
    | "abcdef"  | "abcde"  |
    | "abcdefg" | "abcde"  |
    | "abcdefh" | "abcde"  |


  Scenario Outline: Array out of maximum range
    Given that I send <array>
    And the request is sent as JSON
    When I request "tests/param/minmaxfix/array"
    Then the response status code should be 200
    And the response is JSON
    And the response equals <expected>

  Examples:
    | array             | expected    |
    | [1,2]             | [1,2]       |
    | [1,2,3]           | [1,2,3]     |
    | [1,2,3,4]         | [1,2,3,4]   |
    | [1,2,3,4,5]       | [1,2,3,4,5] |
    | [1,2,3,4,5,6]     | [1,2,3,4,5] |
    | [1,2,3,4,5,6,7]   | [1,2,3,4,5] |
    | [1,2,3,4,5,6,7,8] | [1,2,3,4,5] |

  Scenario Outline: Array short of minimum is not expected
    Given that I send <array>
    And the request is sent as JSON
    When I request "tests/param/minmaxfix/array"
    Then the response status code should be 400
    And the response is JSON

  Examples:
    | array |
    | []    |
    | [1]   |
