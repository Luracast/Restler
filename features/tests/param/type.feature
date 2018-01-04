@param @type
Feature: Type Attribute

  Scenario Outline: valid UUID version 1
    Given that I send {"uuid":<uuid>}
    And the request is sent as JSON
    When I request "/tests/param/type/uuid"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <uuid>

    Examples:
      | uuid                                   |
      | "4b972cba-f138-11e7-8c3f-9a214cf093ae" |
      | "4b973020-f138-11e7-8c3f-9a214cf093ae" |
      | "4b973200-f138-11e7-8c3f-9a214cf093ae" |
      | "4b9733b8-f138-11e7-8c3f-9a214cf093ae" |

  Scenario Outline: valid UUID version 4
    Given that I send {"uuid":<uuid>}
    And the request is sent as JSON
    When I request "/tests/param/type/uuid"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the response equals <uuid>

    Examples:
      | uuid                                   |
      | "040fdd24-cd36-4b94-a5e5-3ad72917686f" |
      | "16bea809-4d60-42b8-9de2-03d464979f74" |
      | "c0b32c01-3892-4202-a955-dc1ae8323ca3" |
      | "fd71a4f8-5423-48b1-863d-fea243503d59" |

  Scenario Outline: Invalid UUID
    Given that I send {"uuid":<uuid>}
    And the request is sent as JSON
    When I request "/tests/param/type/uuid"
    Then the response status code should be 400

    Examples:
      | uuid                                    |
      | "040fdd24-cd36-4b94-a5e5-3ad72917686"   |
      | "16bea8$9-4d60-42b8-9de2-03d464979f_4"  |
      | "c0b32c01-3892-4202-a955-dc1ae8323c"    |
      | "fd71a4f8-5423-48b1-863d-fea243503d593" |

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

  Scenario Outline: An Array of Floating Points
    Given that I send {"numbers":<numbers>}
    And the request is sent as JSON
    When I request "/tests/param/type/numbers"
    Then the response status code should be 200
    And the response is JSON
    And the response equals <expected>

    Examples:
      | numbers            | expected           |
      | []                 | []                 |
      | {}                 | []                 |
      | [1,329.8473984,23] | [1,329.8473984,23] |
      | [0.00001]          | [1.0e-5]           |

  Scenario Outline: An Array of Floating Points as the only body
    Given that I send <numbers>
    And the request is sent as JSON
    When I request "/tests/param/type/numbers"
    Then the response status code should be 200
    And the response is JSON
    And the response equals <expected>

    Examples:
      | numbers            | expected           |
      | []                 | []                 |
      | {}                 | []                 |
      | [1,329.8473984,23] | [1,329.8473984,23] |
      | [0.00001]          | [1.0e-5]           |
      | [0.10e-4]          | [1.0e-5]           |

  Scenario Outline: Invalid Array of Floats as the only body
    Given that I send <numbers>
    And the request is sent as JSON
    When I request "/tests/param/type/numbers"
    Then the response status code should be 400
    And the response is JSON

    Examples:
      | numbers         |
      | ""              |
      | [true,false]    |
      | [null]          |
      | {"key":"value"} |

  Scenario Outline: Custom Class
    Given that I send <author>
    And the request is sent as JSON
    When I request "/tests/param/type/author"
    Then the response status code should be 200
    And the response is JSON
    And the response equals <expected>

    Examples:
      | author                                      | expected                                    |
      | {"name":"Arul"}                             | {"name":"Arul","email":"name@domain.com"}   |
      | {"name":"Arul","email":"arul@luracast.com"} | {"name":"Arul","email":"arul@luracast.com"} |
      | {"name":"name","email":"a@b.c"}             | {"name":"name","email":"a@b.c"}             |

  Scenario Outline: Invalid data for Custom Class
    Given that I send <author>
    And the request is sent as JSON
    When I request "/tests/param/type/author"
    Then the response status code should be 400
    And the response is JSON

    Examples:
      | author                         |
      | ""                             |
      | [true,false]                   |
      | [null]                         |
      | {"key":"value"}                |
      | {"name":"12","email":"a@b.c"}  |
      | {"email":"a@b.c"}              |
      | {"email":"ab.c","name":"1234"} |
      | {"email":"a@b.c","name":"ab"}  |

  Scenario Outline: Array of Custom Class objects
    Given that I send <authors>
    And the request is sent as JSON
    When I request "/tests/param/type/authors"
    Then the response status code should be 200
    And the response is JSON
    And the response equals <expected>

    Examples:
      | authors                                       | expected                                      |
      | [{"name":"Arul"}]                             | [{"name":"Arul","email":"name@domain.com"}]   |
      | [{"name":"Arul","email":"arul@luracast.com"}] | [{"name":"Arul","email":"arul@luracast.com"}] |
      | [{"name":"name","email":"a@b.c"}]             | [{"name":"name","email":"a@b.c"}]             |

  Scenario Outline: Invalid data for array of Custom Class objects
    Given that I send <authors>
    And the request is sent as JSON
    When I request "/tests/param/type/authors"
    Then the response status code should be 400
    And the response is JSON

    Examples:
      | authors                        |
      | ""                             |
      | [true,false]                   |
      | [null]                         |
      | {"key":"value"}                |
      | {"name":"12","email":"a@b.c"}  |
      | {"email":"a@b.c"}              |
      | {"email":"ab.c","name":"1234"} |