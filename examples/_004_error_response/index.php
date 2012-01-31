<?php
/*
 Title: Error Response.
 Tagline: Making use of HTTP status codes.
 Description: API methods can make use of RestException class to provide 
 error information to the user. 
 
 use `throw new RestException($httpStatusCode)` to send the error response 
 to the client. 
 
 For the list of HTTP Status codes and their meaning take a look at 
 [Wikipedia](http://en.wikipedia.org/wiki/Http_status_codes).
 
 Example 1: GET currency/format returns 
 
{
  "error": {
    "code": 400,
    "message": "Bad Request"
  }
}.

 Example 2: GET currency/format/not_a_number returns 
  
{
  "error": {
    "code": 412,
    "message": "Precondition Failed: not a valid number"
  }
}.

 Example 3: GET currency/format?number=55 returns "USD55.00".
*/

require_once '../../restler/restler.php';

#set autoloader
#do not use spl_autoload_register with out parameter
#it will disable the autoloading of formats
spl_autoload_register('spl_autoload');

$r = new Restler();
$r->addAPIClass('Currency');
$r->handle();