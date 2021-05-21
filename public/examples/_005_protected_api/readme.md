## Protected API 

 This example requires `PHP >= 5.4` and tagged under `authentication` `authorization`


Not all the API exposed needs to be public, we need to protect
 some of our API.
 Here are three ways to protect a method


1. Change it to a `protected function`
2. Add a PHPDoc comment `@access protected` to the method
3. Add `@access protected` comment to the class to protect all methods of that
   class


In order to provide access to those protected methods we use a class that
implements `iAuthenticate`. Also note that An Authentication class is also an
API class so all public methods that does not begin with `_` will be exposed as
API for example [SimpleAuth::key](simpleauth/key). It can be used to create
login/logout methods.

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Simple.php      (api)
> * Secured.php      (api)
> * SimpleAuth.php      (auth)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET normal         ⇠ Simple::normal()
    GET restricted     ⇠ Simple::restricted()
    GET restricted2    ⇠ Simple::restricted2()
    GET secured        ⇠ Secured::index()
    GET simpleauth/key ⇠ SimpleAuth::key()






Try the following links in your browser

GET [restricted](index.php/restricted)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "error": {
    "code": 401,
    "message": "Unauthorized"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [restricted?key=rEsTlEr3](index.php/restricted?key=rEsTlEr3)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"protected method"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [secured?key=rEsTlEr3](index.php/secured?key=rEsTlEr3)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"protected class"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




We expect the following behaviour from this example.

```gherkin

@example5 @protected-api
Feature: Testing Protected Api

  Scenario: Calling restricted api without a key
    When I request "examples/_005_protected_api/restricted"
    Then the response status code should be 401

  Scenario: Calling restricted api with invalid key
    When I request "examples/_005_protected_api/restricted?key=not-valid"
    Then the response status code should be 401

  Scenario: Calling restricted api with valid key
    When I request "examples/_005_protected_api/restricted?key=rEsTlEr3"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected method"

  Scenario: Calling restricted api class with valid key
    When I request "examples/_005_protected_api/secured?key=rEsTlEr3"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected class"

```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
bin/behat  features/examples/_005_protected_api.feature
```



*[index.php]: _005_protected_api/index.php
*[Simple.php]: _005_protected_api/Simple.php
*[Secured.php]: _005_protected_api/Secured.php
*[SimpleAuth.php]: _005_protected_api/SimpleAuth.php
*[restler.php]: ../../restler.php
*[JsonFormat.php]: ../../src/Format/JsonFormat.php

