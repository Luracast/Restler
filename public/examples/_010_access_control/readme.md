## Access Control 

 This example requires `PHP >= 5.4` and tagged under `access-control` `acl` `secure` `authentication` `authorization`


This example shows how you can extend the authentication system to create
a robust access control system. As a added bonus we also restrict api
documentation based on the same.

When the `api_key` is

- blank you will see the public api
- `12345` you will see the api that is accessible by an user
- `67890` you will see all api as you have the admin rights

Try it out yourself [here](explorer/index.html#!/v1)

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Access.php      (api)
> * AccessControl.php      (auth)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET admin            ⇠ Access::admin()
    GET all              ⇠ Access::all()
    GET explorer/*       ⇠ Luracast\Restler\Explorer\v2\Explorer::get()
    GET explorer/swagger ⇠ Luracast\Restler\Explorer\v2\Explorer::swagger()
    GET user             ⇠ Access::user()







We expect the following behaviour from this example.

```gherkin

@example10 @access-control
Feature: Testing Access Control

  Scenario: Access public api without a key
    When I request "examples/_010_access_control/all"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "public api, all are welcome"

  Scenario: Access public api with user key
    When I request "examples/_010_access_control/all?api_key=12345"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "public api, all are welcome"

  Scenario: Access public api with admin key
    When I request "examples/_010_access_control/all?api_key=67890"
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
    When I request "examples/_010_access_control/user?api_key=12345"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected api, only user and admin can access"

  Scenario: Access user api with admin key
    When I request "examples/_010_access_control/user?api_key=67890"
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
    When I request "examples/_010_access_control/admin?api_key=12345"
    Then the response status code should be 401
    And the response is JSON
    And the "error.message" property equals "Unauthorized"

  Scenario: Access admin api with admin key
    When I request "examples/_010_access_control/admin?api_key=67890"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "protected api, only admin can access"

```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
vendor/bin/behat  features/examples/_010_access_control.feature
```



*[index.php]: _010_access_control/index.php
*[Access.php]: _010_access_control/Access.php
*[AccessControl.php]: _010_access_control/AccessControl.php
*[restler.php]: ../../restler.php
*[JsonFormat.php]: ../../src/Format/JsonFormat.php

