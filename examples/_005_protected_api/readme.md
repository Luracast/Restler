Protected API
-------------

Not all the API exposed needs to be public, we need to protect some of our API. 
Here are three ways to protect a method

1. Change it to a `protected function`
2. Add a PHPDoc comment `@protected` to the method
3. Add `@protected` comment to the class to protect all methods of that class

In order to provide access to those protected methods we use a class that implements `iAuthenticate`. Also note that
An Authentication class is also an API class so all public methods that does not begin with `_` will be exposed as API
for example [SimpleAuth::key](simpleauth/key). It can be used to create login/logout methods
> This API Server is made using the following php files/folders

> * index.php      (gateway)
> * simple.php      (api)
> * secured.php      (api)
> * simpleauth.php      (auth)
> * restler.php      (framework)

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
*[simple.php]: _005_protected_api/simple.php
*[secured.php]: _005_protected_api/secured.php
*[simpleauth.php]: _005_protected_api/simpleauth.php
*[restler.php]: ../restler/restler.php
