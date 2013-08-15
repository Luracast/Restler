## Access Control 

 This example requires `PHP >= 5.3` and taggeed under `access-control` `acl` `secure` `authentication` `authorization`


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
> * Resources.php      (api)
> * AccessControl.php      (auth)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET admin          ⇠ Access::admin()
    GET all            ⇠ Access::all()
    GET resources      ⇠ Luracast\Restler\Resources::index()
    GET resources/{id} ⇠ Luracast\Restler\Resources::get()
    GET user           ⇠ Access::user()








*[index.php]: _010_access_control/index.php
*[Access.php]: _010_access_control/Access.php
*[Resources.php]: ../../vendor/Luracast/Restler/Resources.php
*[AccessControl.php]: _010_access_control/AccessControl.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

