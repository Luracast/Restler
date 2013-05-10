Protected API <requires>PHP >= 5.3</requires>
-------------
<tag>authentication</tag> <tag>authorization</tag> 

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

GET [restricted?key=rEsTlEr2](index.php/restricted?key=rEsTlEr2)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"protected method"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [secured?key=rEsTlEr2](index.php/secured?key=rEsTlEr2)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"protected class"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~





*[index.php]: _005_protected_api/index.php
*[Simple.php]: _005_protected_api/Simple.php
*[Secured.php]: _005_protected_api/Secured.php
*[SimpleAuth.php]: _005_protected_api/SimpleAuth.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

