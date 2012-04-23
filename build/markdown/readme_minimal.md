Hello World
-----------

First step to know more about Restler2.
Shows the bare minimum code needed to get your RESTful api server up and running

This API Server exposes the following URIs

	GET say/hello        ⇠ Say::hello()
	GET say/hello/:to    ⇠ Say::hello()


Try the following links in your browser

GET [say/hello](say/hello)
:	``

GET [say/hello/:to](say/hello/:to)
:	``


If the above links fail, it could be due to missing `.htaccess` file or URL Rewriting is not supported in your server. 
Try the following links instead

GET [index.php/say/hello](index.php/say/hello)
:	``

GET [index.php/say/hello/:to](index.php/say/hello/:to)
:	``


> Makes use of *index.php, math.php and restler.php*

Helloworld Example
------------------

GET [say/hello](say/hello) ▾
:   `{"result":12}`

Orange
:   The fruit of an evergreen tree of the genus Citrus.

This API Server exposes the following URIs

	GET say/hello			← Say::hello()
	GET say/hello/:to		← Say::hello()


> Makes use of *index.php, say.php and restler.php*

Minimal Example
---------------
Shows the bare minimum code needed to get your RESTful api server up and running

This API Server exposes the following URIs

	GET math/add			← Math::add()
	GET math/add/:n1		← Math::add()
	GET math/add/:n1/:n2		← Math::add()
	GET math/multiply		← Math::multiply()
	GET math/multiply/:n1	← Math::multiply()
	GET math/multiply/:n1/:n2	← Math::multiply()


Try the following links in your browser

* GET [math/add](math/add) → `2`
* GET [math/add/4/3](math/add/4/3) → `7`
* GET [math/add?n1=6&n2=4](math/add?n1=6&n2=4) → `10`
* GET [math/multiply](math/multiply) → `{"result":10}`
* GET [math/multiply/4/3](math/multiply/4/3) → `{"result":12}`
* GET [math/multiply?n2=4](math/multiply?n2=4) → `{"result":20}`

If the above links fail, it could be due to missing `.htaccess` file or URL Rewriting is not supported in your server. Try the following links instead

* GET [index.php/math/add](index.php/math/add) → `2`
* GET [index.php/math/add/4/3](index.php/math/add/4/3) → `7`
* GET [index.php/math/add?n1=6&n2=4](index.php/math/add?n1=6&n2=4) → `10`
* GET [index.php/math/multiply](index.php/math/multiply) → `{"result":10}`
* GET [index.php/math/multiply/4/3](index.php/math/multiply/4/3) → `{"result":12}`
* GET [index.php/math/multiply?n2=4](index.php/math/multiply?n2=4) → `{"result":20}`

> Makes use of *index.php, math.php and restler.php*

Helloworld Example
------------------

This API Server exposes the following URIs

	GET ./				← HelloWorld::index()
	GET sum				← HelloWorld::sum()
	GET sum/:num1			← HelloWorld::sum()
	GET sum/:num1/:num2		← HelloWorld::sum()
	GET multiply			← HelloWorld::multiply()
	GET multiply/:num1		← HelloWorld::multiply()
	GET multiply/:num1/:num2	← HelloWorld::multiply()
	GET subtract			← HelloWorld::subtract()
	GET subtract/:n1		← HelloWorld::subtract()
	GET subtract/:n1/:n2		← HelloWorld::subtract()


> Makes use of *index.php, helloworld.php, simpleauth.php and restler.php*
