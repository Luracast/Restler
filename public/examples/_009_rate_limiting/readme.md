Rate Limiting <requires>PHP >= 5.3</requires>
-------------
<tag>create</tag> <tag>retrieve</tag> <tag>read</tag> <tag>update</tag> <tag>delete</tag> <tag>post</tag> <tag>get</tag> <tag>put</tag> <tag>filter</tag> <tag>throttle</tag> <tag>rate-limiting</tag> 

How to Rate Limit API access using a Filter class that implements
`iFilter` interface.

This example also shows how to use Defaults class to customize defaults, how to create your own
iCache implementation, and how to make a hybrid filter class that behaves deferently
when the user is Authenticated

[![Restler API Explorer](../resources/explorer1.png)](explorer/index.html#!/authors-v1)

Key in `r3rocks` as the API key in the Explorer to see how rate limit changes

We are progressively improving the Authors class from CRUD example 
to show Best Practices and Restler 3 Features.

Make sure you compare them to understand.

> **Note:-** Using session variables as DB and Cache is useless for real life and wrong. We are using it
Only for demo purpose. Since API Explorer is browser based it works well with that.

If you have hit the API Rate Limit or screwed up the Authors DB, you can easily reset by deleting
PHP_SESSION cookie using the Developer Tools in your browser.

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Resources.php      (api)
> * RateLimit.php      (filter)
> * SessionCache.php      (helper)
> * Authors.php      (api)
> * KeyAuth.php      (auth)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET    authors                   ⇠ ratelimited\Authors::index()
    GET    authors/{id}              ⇠ ratelimited\Authors::get()
    GET    resources/{id}-v{version} ⇠ Resources::get()
    GET    resources/v{version}      ⇠ Resources::get()
    GET    resources                 ⇠ Resources::index()
    POST   authors                   ⇠ ratelimited\Authors::post()
    PUT    authors/{id}              ⇠ ratelimited\Authors::put()
    PATCH  authors/{id}              ⇠ ratelimited\Authors::patch()
    DELETE authors/{id}              ⇠ ratelimited\Authors::delete()








*[index.php]: _009_rate_limiting/index.php
*[Resources.php]: ../../vendor/Luracast/Restler/Resources.php
*[RateLimit.php]: ../../vendor/Luracast/Restler/Filter/RateLimit.php
*[SessionCache.php]: _009_rate_limiting/SessionCache.php
*[Authors.php]: _009_rate_limiting/ratelimited/Authors.php
*[KeyAuth.php]: _009_rate_limiting/KeyAuth.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

