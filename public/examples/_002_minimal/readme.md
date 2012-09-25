Minimal Example <requires>PHP >= 5.3</requires>
---------------

<tag>basic</tag>
<tag>validation</tag>
<tag>http status</tag>

Shows the bare minimum code needed to get your RESTful api server
 up and running

> **Note:-** Take note of the php doc comments, they make sure the data is sent
  in the right type validated automatically before calling the api method.
> This API Server is made using the following php files/folders

> * index.php      (gateway)
> * Math.php      (api)
> * restler.php      (framework)

This API Server exposes the following URIs

    GET math/add                ⇠ Math::add()
    GET math/multiply/{n1}/{n2} ⇠ Math::multiply()


Try the following links in your browser

GET [math/add](index.php/math/add)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
2
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/add?n1=6&n2=4](index.php/math/add?n1=6&n2=4)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
10
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/multiply/4/3](index.php/math/multiply/4/3)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{"result":12}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/multiply/4/NaN](index.php/math/multiply/4/NaN)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "error": {
    "code": 400,
    "message": "Bad Request: invalid value specified for n2"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~





*[index.php]: _002_minimal/index.php
*[Math.php]: _002_minimal/Math.php
*[restler.php]: ../../vendor/restler.php

