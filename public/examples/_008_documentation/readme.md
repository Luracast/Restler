## Documentation 

 This example requires `PHP >= 5.4` and tagged under `create` `retrieve` `read` `update` `delete` `post` `get` `put` `routing` `doc` `production` `debug`


How to document and let your users explore your API.
We have modified SwaggerUI to create
[Restler API Explorer](https://github.com/Luracast/Restler-API-Explorer)
which is used [here](explorer/index.html#!/authors-v1).

[![Restler API Explorer](../resources/explorer1.png)](explorer/index.html#!/authors-v1)

We are progressively improving the Authors class from CRUD example
to Rate Limiting Example to show Best Practices and Restler 3 Features.

Make sure you compare them to understand.

Even though API Explorer is created with API consumers in mind, it will help the
API developer with routing information and commenting assistance when  our API
class is not fully commented as in this example. This works only on the debug
mode. Try changing rester to run in production mode (`$r = new Restler(true)`)

> **Note:-** production mode writes human readable cache file for the routes in
> the cache directory by default. So make sure cache folder has necessary
> write permission.

Happy Exploring! :)

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Authors.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET    authors          ⇠ improved\Authors::index()
    POST   authors          ⇠ improved\Authors::post()
    PATCH  authors/reset    ⇠ improved\Authors::patchReset()
    GET    authors/{id}     ⇠ improved\Authors::get()
    PUT    authors/{id}     ⇠ improved\Authors::put()
    PATCH  authors/{id}     ⇠ improved\Authors::patch()
    DELETE authors/{id}     ⇠ improved\Authors::delete()
    GET    explorer/*       ⇠ Luracast\Restler\Explorer\v2\Explorer::get()
    GET    explorer/swagger ⇠ Luracast\Restler\Explorer\v2\Explorer::swagger()







We expect the following behaviour from this example.

```gherkin

@example8 @documentation
Feature: Testing Documentation Example

  Scenario: Resetting data to begin tests
    When I request "PATCH examples/_008_documentation/authors/reset.json"
    Then the response status code should be 200
    And the response should be JSON
    And the response equals true

  Scenario: Creating new Author by POSTing vars
    Given that I want to make a new "Author"
    And his "name" is "Chris"
    And his "email" is "chris@world.com"
    When I request "examples/_008_documentation/authors"
    Then the response status code should be 201
    And the response should be JSON
    And the response has a "id" property

  Scenario: Creating new Author with JSON
    Given that I want to make a new "Author"
    And his "name" is "Chris"
    And his "email" is "chris@world.com"
    And the request is sent as JSON
    When I request "examples/_008_documentation/authors"
    Then the response status code should be 201
    And the response should be JSON
    And the response has a "id" property

  Scenario: Updating Author with JSON
    Given that I want to update "Author"
    And his "name" is "Jac"
    And his "email" is "jac@wright.com"
    And his "id" is 1
    And the request is sent as JSON
    When I request "examples/_008_documentation/authors/{id}"
    Then the response status code should be 200
    And the response should be JSON
    And the response has a "id" property

  Scenario: Given url is valid for other http method(s)
    Given that I want to update "Author"
    And his "name" is "Jac"
    And his "email" is "jac@wright.com"
    And his "id" is 1
    And the request is sent as JSON
    When I request "examples/_008_documentation/authors"
    Then the response status code should be 405
    And the response "Allow" header should be "GET, POST"

  Scenario: Deleting Author
    Given that I want to delete an "Author"
    And his "id" is 1
    When I request "examples/_008_documentation/authors/{id}"
    Then the response status code should be 200
    And the response should be JSON
    And the response has an "id" property

  Scenario: Deleting with invalid author id
    Given that I want to delete an "Author"
    And his "id" is 1
    When I request "examples/_008_documentation/authors/{id}"
    Then the response status code should be 404
    And the response should be JSON

  Scenario: Checking Redirect of Explorer
    When I request "examples/_008_documentation/explorer"
    Then the response redirects to "examples/_008_documentation/explorer/"
    And the response should be HTML

```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
bin/behat  features/examples/_008_documentation.feature
```



*[index.php]: _008_documentation/index.php
*[Authors.php]: _008_documentation/improved/Authors.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

