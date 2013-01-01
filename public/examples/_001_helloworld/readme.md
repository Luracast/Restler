Hello World Example <requires>PHP >= 5.3</requires>
-------------------
<tag>basic</tag> 

Basic hello world example to get started with Restler 3.

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Say.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET say/hello   ⇠ Say::hello()
    GET say/hi/{to} ⇠ Say::hi()


> **Note:-** If you have used Restler 2 before, you will wonder why
 the generated routes are lesser with Restler 3.
 Read the [Routes](../_006_routing/readme.html) example to understand.



Try the following links in your browser

GET [say/hello](say/hello)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello world!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [say/hello?to=R.Arul%20Kumaran](say/hello?to=R.Arul%20Kumaran)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello R.Arul Kumaran!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [say/hi/restler3.0](say/hi/restler3.0)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hi restler3.0!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


If the above links fail, it could be due to missing `.htaccess` file or URL
Rewriting is not supported in your server. If you are on Apache Server, make sure
`AllowOverride` is set to `All` instead of `None` in the `httpd.conf` file.

In case you could not get URL Rewriting to work, try the following links instead

GET [index.php/say/hello](index.php/say/hello)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello world!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [index.php/say/hello?to=R.Arul%20Kumaran](index.php/say/hello?to=R.Arul%20Kumaran)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello R.Arul Kumaran!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [index.php/say/hi/restler3.0](index.php/say/hi/restler3.0)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hi restler3.0!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




We expect the following behaviour from this example.

```gherkin

@example1 @helloworld
Feature: Testing Helloworld Example

  Scenario: Saying Hello world
    When I request "/examples/_001_helloworld/say/hello"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "Hello world!"

  Scenario: Saying Hello Restler
    Given that "to" is set to "Restler"
    When I request "/examples/_001_helloworld/say/hello{?to}"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "Hello Restler!"

  Scenario: Saying
    When I request "/examples/_001_helloworld/say"
    Then the response status code should be 404
    And the response is JSON
    And the type is "array"

  Scenario: Saying Hi
    When I request "/examples/_001_helloworld/say/hi"
    Then the response status code should be 404
    And the response is JSON
    And the type is "array"

  Scenario: Saying Hi Arul
    Given that "to" is set to "Arul"
    When I request "/examples/_001_helloworld/say/hi/{to}"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "Hi Arul!"
```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
bin/behat  features/examples/_001_helloworld.feature
```



*[index.php]: _001_helloworld/index.php
*[Say.php]: _001_helloworld/Say.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php
