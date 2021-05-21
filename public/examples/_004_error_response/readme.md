## Error Response 

 This example requires `PHP >= 5.4` and tagged under `exception` `http status` `validation`


API methods can make use of RestException class to provide
 error information to the user.

 use `throw new RestException($httpStatusCode)` to send the error response
 to the client.

 For the list of HTTP Status codes and their meaning take a look at
 [Wikipedia](http://en.wikipedia.org/wiki/Http_status_codes)

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Currency.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET currency/format ⇠ Currency::format()






Try the following links in your browser

GET [currency/format](index.php/currency/format)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "error": {
    "code": 400,
    "message": "Bad Request"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [currency/format?number=not_a_number](index.php/currency/format?number=not_a_number)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "error": {
    "code": 400,
    "message": "Bad Request: not a valid number"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [currency/format?number=55](index.php/currency/format?number=55)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"USD55.00"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




We expect the following behaviour from this example.

```gherkin

@example4 @error-response
Feature: Testing Error Response

  Scenario: Calling currency format without a number
    When I request "examples/_004_error_response/currency/format"
    Then the response status code should be 400

  Scenario: Calling currency format with invalid number
    When I request "examples/_004_error_response/currency/format?number=not_a_number"
    Then the response status code should be 400

  Scenario: Calling currency format with invalid number
    When I request "examples/_004_error_response/currency/format?number=55"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "$55.00"

```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
bin/behat  features/examples/_004_error_response.feature
```



*[index.php]: _004_error_response/index.php
*[Currency.php]: _004_error_response/Currency.php
*[restler.php]: ../../restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

