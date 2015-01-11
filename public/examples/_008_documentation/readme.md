## Documentation 

 This example requires `PHP >= 5.3` and taggeed under `create` `retrieve` `read` `update` `delete` `post` `get` `put` `routing` `doc` `production` `debug`


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
> * Resources.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET    authors                ⇠ improved\Authors::index()
    POST   authors                ⇠ improved\Authors::post()
    DELETE authors/{id}           ⇠ improved\Authors::delete()
    PATCH  authors/{id}           ⇠ improved\Authors::patch()
    PUT    authors/{id}           ⇠ improved\Authors::put()
    GET    authors/{id}           ⇠ improved\Authors::get()
    GET    resources              ⇠ Luracast\Restler\Resources::index()
    GET    resources/verifyaccess ⇠ Luracast\Restler\Resources::verifyAccess()
    GET    resources/{id}         ⇠ Luracast\Restler\Resources::get()








*[index.php]: _008_documentation/index.php
*[Authors.php]: _008_documentation/improved/Authors.php
*[Resources.php]: ../../vendor/Luracast/Restler/Resources.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

