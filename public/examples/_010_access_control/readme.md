Access Control <requires>PHP >= 5.3</requires>
--------------
<tag>access-control</tag> <tag>acl</tag> <tag>secure</tag> <tag>authentication</tag> <tag>authorization</tag> 

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
> * Resources.php      (api)
> * Access.php      (api)
> * AccessControl.php      (auth)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET admin                     ⇠ Access::admin()
    GET all                       ⇠ Access::all()
    GET resources                 ⇠ Resources::index()
    GET resources/v{version}      ⇠ Resources::get()
    GET resources/{id}-v{version} ⇠ Resources::get()
    GET user                      ⇠ Access::user()








*[index.php]: _010_access_control/index.php
*[Resources.php]: ../../vendor/Luracast/Restler/Resources.php
*[Access.php]: _010_access_control/Access.php
*[AccessControl.php]: _010_access_control/AccessControl.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

