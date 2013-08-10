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

  Scenario Outline: Email as the only body
    Given that I send <email>
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

  Scenario Outline: Invalid Email
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

  Scenario Outline: Invalid Email as the only body
    Given that I send <email>
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

  Scenario Outline: Date as the only body
    Given that I send <date>
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

  Scenario Outline: Invalid Date
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

  Scenario Outline: Invalid Date as the only body
    Given that I send <date>
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

  Scenario Outline: Invalid Date and Time
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

  Scenario Outline: Time Stamp
    Given that I send {"timestamp":<timestamp>}
    And the request is sent as JSON
    When I request "/tests/param/type/timestamp"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <expected>

  Examples:
    | timestamp    | expected   |
    | "1376094488" | 1376094488 |
    | 1376094488   | 1376094488 |
    | "0123"       | 123        |
    | 123.0        | 123        |
    | 1            | 1          |
    | 0            | 0          |

  Scenario Outline: Time Stamp as the only body
    Given that I send <timestamp>
    And the request is sent as JSON
    When I request "/tests/param/type/timestamp"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <expected>

  Examples:
    | timestamp    | expected   |
    | "1376094488" | 1376094488 |
    | 1376094488   | 1376094488 |
    | "0123"       | 123        |
    | 123.0        | 123        |
    | 1            | 1          |

  Scenario Outline: Invalid Time Stamp
    Given that I send {"timestamp":<timestamp>}
    And the request is sent as JSON
    When I request "/tests/param/type/timestamp"
    Then the response status code should be 400
    And the response is JSON

  Examples:
    | timestamp             |
    | "2013-08-10"          |
    | "2013-08-10 00:34:18" |
    | "0xFF"                |
    | 1.1                   |

  Scenario Outline: Invalid Time Stamp as the only body
    Given that I send <timestamp>
    And the request is sent as JSON
    When I request "/tests/param/type/timestamp"
    Then the response status code should be 400
    And the response is JSON

  Examples:
    | timestamp             |
    | "2013-08-10"          |
    | "2013-08-10 00:34:18" |
    | "0xFF"                |
    | 1.1                   |

  Scenario Outline: An Array of Integers
    Given that I send {"integers":<integers>}
    And the request is sent as JSON
    When I request "/tests/param/type/integers"
    Then the response status code should be 200
    And the response is JSON
    And the response equals <expected>

  Examples:
    | integers          | expected          |
    | []                | []                |
    | {}                | []                |
    | [1,3298473984,23] | [1,3298473984,23] |
    | [0]               | [0]               |

  Scenario Outline: An Array of Integers as the only body
    Given that I send <integers>
    And the request is sent as JSON
    When I request "/tests/param/type/integers"
    Then the response status code should be 200
    And the response is JSON
    And the response equals <expected>

  Examples:
    | integers          | expected          |
    | []                | []                |
    | {}                | []                |
    | [1,3298473984,23] | [1,3298473984,23] |
    | [0]               | [0]               |

  Scenario Outline: Invalid Array of Integers
    Given that I send {"integers":<integers>}
    And the request is sent as JSON
    When I request "/tests/param/type/integers"
    Then the response status code should be 400
    And the response is JSON

  Examples:
    | integers        |
    | ""              |
    | [true,false]    |
    | [null]          |
    | [1.34]          |
    | {"key":"value"} |

  Scenario Outline: Invalid Array of Integers as the only body
    Given that I send <integers>
    And the request is sent as JSON
    When I request "/tests/param/type/integers"
    Then the response status code should be 400
    And the response is JSON

  Examples:
    | integers        |
    | ""              |
    | [true,false]    |
    | [null]          |
    | [1.34]          |
    | {"key":"value"} |