## Rate Limiting 

 This example requires `PHP >= 5.3` and taggeed under `create` `retrieve` `read` `update` `delete` `post` `get` `put` `filter` `throttle` `rate-limiting`


How to Rate Limit API access using a Filter class that implements
`iFilter` interface.

This example also shows how to use Defaults class to customize defaults, how to create your own
iCache implementation, and how to make a hybrid filter class that behaves differently
when the user is Authenticated

[![Restler API Explorer](../resources/explorer1.png)](explorer/index.html#!/authors-v1)

Key in `r3rocks` as the API key in the Explorer to see how rate limit changes

We are progressively improving the Authors class from CRUD example 
to show Best Practices and Restler 3 Features.

Make sure you compare them to understand.

> **Note:-**
>
>  1. Using session variables as DB and Cache is useless for real life and wrong. We are using it
>     Only for demo purpose. Since API Explorer is browser based it works well with that.
>
>  2. We are using Author.php to document return type of `GET authors/{id}` using `@return` comment

If you have hit the API Rate Limit or screwed up the Authors DB, you can easily reset by deleting
PHP_SESSION cookie using the Developer Tools in your browser.

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * RateLimit.php      (filter)
> * SessionCache.php      (helper)
> * Authors.php      (api)
> * Resources.php      (api)
> * KeyAuth.php      (auth)
> * Author.php      (helper)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET    authors                ⇠ ratelimited\Authors::index()
    POST   authors                ⇠ ratelimited\Authors::post()
    DELETE authors/{id}           ⇠ ratelimited\Authors::delete()
    PATCH  authors/{id}           ⇠ ratelimited\Authors::patch()
    PUT    authors/{id}           ⇠ ratelimited\Authors::put()
    GET    authors/{id}           ⇠ ratelimited\Authors::get()
    GET    resources              ⇠ Luracast\Restler\Resources::index()
    GET    resources/verifyaccess ⇠ Luracast\Restler\Resources::verifyAccess()
    GET    resources/{id}         ⇠ Luracast\Restler\Resources::get()







We expect the following behaviour from this example.

```gherkin

@example9 @crud
Feature: Testing Rate Limiting Example

  Scenario: Failing to delete missing Author with JSON
    Given that I want to delete an "Author"
    And his "id" is 2000
    When I request "/examples/_009_rate_limiting/authors/{id}?api_key=r3rocks"
    Then the response status code should be 404
```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
bin/behat  features/examples/_009_rate_limiting.feature
```



*[index.php]: _009_rate_limiting/index.php
*[RateLimit.php]: ../../vendor/Luracast/Restler/Filter/RateLimit.php
*[SessionCache.php]: _009_rate_limiting/SessionCache.php
*[Authors.php]: _009_rate_limiting/ratelimited/Authors.php
*[Resources.php]: ../../vendor/Luracast/Restler/Resources.php
*[KeyAuth.php]: _009_rate_limiting/KeyAuth.php
*[Author.php]: _009_rate_limiting/Author.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

*[Author.php]: _009_rate_limiting/Author.php