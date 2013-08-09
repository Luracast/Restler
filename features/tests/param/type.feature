@param @type
Feature: Type Attribute

  Scenario Outline: Email
    Given that I send {"email":<email>}
    And the request is sent as JSON
    When I request "/tests/param/type/email"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <email>

  Examples:
    | email                 |
    | "$A12345@example.com" |
    | "!def!xyz%abc@my.co"  |
    | "sane@h.io"           |

  Scenario Outline: Bad Email
    Given that I send {"email":<email>}
    And the request is sent as JSON
    When I request "/tests/param/type/email"
    Then the response status code should be 400
    And the response is JSON

  Examples:
    | email              |
    | "some.thing@wrong" |
    | "missing@dot"      |
    | "missing.at"       |