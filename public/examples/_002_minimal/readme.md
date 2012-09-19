Minimal Example <requires>PHP >= 5.3</requires>
---------------
<tag>basic</tag> <tag>validation</tag> <tag>http status</tag> 

Shows the bare minimum code needed to get your RESTful api server
 up and running

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Math.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET math/add                ⇠ Math::add()
    GET math/multiply/{n1}/{n2} ⇠ Math::multiply()


> **Note:-** Take note of the php doc comments, they make sure the
 data is sent in the right type and validated automatically before calling
 the api method.



Try the following links in your browser

GET [math/add](index.php/math/add)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
2
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/add?n1=6&n2=4](index.php/math/add?n1=6&n2=4)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
10
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/multiply/4/3](index.php/math/multiply/4/3)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{"result":12}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/multiply/4/NaN](index.php/math/multiply/4/NaN)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "error": {
    "code": 400,
    "message": "Bad Request: invalid value specified for n2"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




We expect the following behaviour from this example.

```gherkin

@example2 @minimal
Feature: Testing Minimal Example

  Scenario: Add using Default Values
    When I request "/examples/_002_minimal/math/add"
    Then the response status code should be 200
    And the response is JSON
    And the type is "int"
    And the value equals 2

  Scenario: Add 5 and 10
    Given that "n1" is set to "5"
    And "n2" is set to "10"
    When I request "/examples/_002_minimal/math/add{?n1,n2}"
    Then the response status code should be 200
    And the response is JSON
    And the type is "int"
    And the value equals 15

  Scenario: Verify Validation
    Given that "n1" is set to "NaN"
    When I request "/examples/_002_minimal/math/add{?n1,n2}"
    Then the response status code should be 400
    And the response is JSON
    And the response has a "error" property

  Scenario: Multiply
    Given that "n1" is set to "10"
    And "n2" is set to "5"
    When I request "/examples/_002_minimal/math/multiply/{n1}/{n2}"
    And the response is JSON
    And the response has a "result" property
    And the "result" property equals 50

  Scenario: Multiply without value
    When I request "/examples/_002_minimal/math/multiply"
    Then the response status code should be 404
    And the response is JSON
    And the type is "array"
    And the response has a "error" property

  Scenario: Verify Validation for multiplication
    Given that "n1" is set to "NaN"
    And "n2" is set to "5"
    When I request "/examples/_002_minimal/math/multiply/{n1}/{n2}"
    Then the response status code should be 400
    And the response is JSON
    And the response has a "error" property

```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
bin/behat  features/examples/_002_minimal.feature
```



*[index.php]: _002_minimal/index.php
*[Math.php]: _002_minimal/Math.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

