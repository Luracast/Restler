<?php
/*
 Title: Protected API.
 Tagline: Creating restricted zone.
 Description: Not all the API exposed needs to be public, we need to protect some of our API. 
 Here are three ways to protect a method
 
 1. Change it to a `protected function`
 2. Add a PHPDoc comment `@protected` to the method
 3. Add `@protected` comment to the class to protect all methods of that class
 
 In order to provide access to those protected methods we use a class that implements `iAuthenticate`. Also note that
 An Authentication class is also an API class so all public methods that does not begin with `_` will be exposed as API
 for example [SimpleAuth::key](simpleauth/key). It can be used to create login/logout methods.
 
 
 Example 1: GET restricted returns 
 
{
  "error": {
    "code": 401,
    "message": "Unauthorized"
  }
}.

 Example 2: GET restricted?key=rEsTlEr2 returns "protected method".
 
 Example 3: GET secured?key=rEsTlEr2 returns "protected class".
*/

require_once '../../restler/restler.php';

#set autoloader
#do not use spl_autoload_register with out parameter
#it will disable the autoloading of formats
spl_autoload_register('spl_autoload');

$r = new Restler();

$r->addAPIClass('Simple','');
$r->addAPIClass('Secured');
$r->addAuthenticationClass('SimpleAuth');
$r->handle();