Minimal Example
---------------

<tag>basic</tag>
<tag>validation</tag>
<tag>http status</tag>

Shows the bare minimum code needed to get your RESTful api server up and running
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






*[index.php]: _002_minimal/index.php
*[Math.php]: _002_minimal/math.php
*[restler.php]: ../restler/restler.php

