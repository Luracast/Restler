Minimal Example
---------------

Shows the bare minimum code needed to get your RESTful api server up and running
> This API Server is made using the following php files/folders

> * index.php      (gateway)
> * math.php      (api)
> * restler.php      (framework)

This API Server exposes the following URIs

	GET math/add              ⇠ Math::add()
	GET math/add/:n1          ⇠ Math::add()
	GET math/add/:n1/:n2      ⇠ Math::add()
	GET math/multiply         ⇠ Math::multiply()
	GET math/multiply/:n1     ⇠ Math::multiply()
	GET math/multiply/:n1/:n2 ⇠ Math::multiply()


Try the following links in your browser

GET [math/add](index.php/math/add)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
2
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/add/4/3](index.php/math/add/4/3)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
7
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/add?n1=6&n2=4](index.php/math/add?n1=6&n2=4)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
10
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/multiply](index.php/math/multiply)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{"result":10}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/multiply/4/3](index.php/math/multiply/4/3)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{"result":12}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [math/multiply?n2=4](index.php/math/multiply?n2=4)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{"result":20}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




*[index.php]: _002_minimal/index.php
*[math.php]: _002_minimal/math.php
*[restler.php]: ../restler/restler.php
