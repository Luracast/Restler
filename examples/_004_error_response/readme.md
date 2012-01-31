Error Response
--------------

API methods can make use of RestException class to provide 
error information to the user. 

use `throw new RestException($httpStatusCode)` to send the error response 
to the client. 

For the list of HTTP Status codes and their meaning take a look at 
[Wikipedia](http://en.wikipedia.org/wiki/Http_status_codes)
> This API Server is made using the following php files/folders

> * index.php      (gateway)
> * currency.php      (api)
> * restler.php      (framework)

This API Server exposes the following URIs

	GET currency/format         ⇠ Currency::format()
	GET currency/format/:number ⇠ Currency::format()


Try the following links in your browser

GET [currency/format](index.php/currency/format)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
 "error": {
   "code": 400,
   "message": "Bad Request"
 }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [currency/format/not_a_number](index.php/currency/format/not_a_number)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
 "error": {
   "code": 412,
   "message": "Precondition Failed: not a valid number"
 }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [currency/format?number=55](index.php/currency/format?number=55)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"USD55.00"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




*[index.php]: _004_error_response/index.php
*[currency.php]: _004_error_response/currency.php
*[restler.php]: ../restler/restler.php
