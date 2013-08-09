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

  Scenario Outline: Date
    Given that I send {"date":<date>}
    And the request is sent as JSON
    When I request "/tests/param/type/date"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <date>

  Examples:
    | date         |
    | "2013-08-10" |
    | "1974-03-30" |
    | "1600-12-17" |

  Scenario Outline: Bad Date
    Given that I send {"date":<date>}
    And the request is sent as JSON
    When I request "/tests/param/type/date"
    Then the response status code should be 400
    And the response is JSON

  Examples:
    | date         |
    | "YYY-MM-DD"  |
    | "10-08-2013" |
    | "2013-13-10" |
    | "2013-02-30" |
    | "2013/08/10" |

  Scenario Outline: Date and Time
    Given that I send {"datetime":<datetime>}
    And the request is sent as JSON
    When I request "/tests/param/type/datetime"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <datetime>

  Examples:
    | datetime              |
    | "2013-08-10 00:34:18" |

  Scenario Outline: Bad Date and Time
    Given that I send {"datetime":<datetime>}
    And the request is sent as JSON
    When I request "/tests/param/type/datetime"
    Then the response status code should be 400
    And the response is JSON

  Examples:
    | datetime              |
    | "2013-08-10"          |
    | "2013-08-10 25:70:11" |
    | "2013/08/10 00:34:18" |