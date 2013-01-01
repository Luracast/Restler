Error Response <requires>PHP >= 5.3</requires>
--------------
<tag>exception</tag> <tag>http status</tag> <tag>validation</tag> 

API methods can make use of RestException class to provide
 error information to the user.

 use `throw new RestException($httpStatusCode)` to send the error response
 to the client.

 For the list of HTTP Status codes and their meaning take a look at
 [Wikipedia](http://en.wikipedia.org/wiki/Http_status_codes)

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Currency.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET currency/format ⇠ Currency::format()






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

GET [currency/format?number=not_a_number](index.php/currency/format?number=not_a_number)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "error": {
    "code": 400,
    "message": "Bad Request: not a valid number"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [currency/format?number=55](index.php/currency/format?number=55)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"USD55.00"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~





*[index.php]: _004_error_response/index.php
*[Currency.php]: _004_error_response/Currency.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

