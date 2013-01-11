Hello World Example
-------------------

Basic hello world example to get started with Restler 2.0
> This API Server is made using the following php files/folders

> * index.php      (gateway)
> * say.php      (api)
> * restler.php      (framework)

This API Server exposes the following URIs

	GET say/hello     ⇠ Say::hello()
	GET say/hello/:to ⇠ Say::hello()


Try the following links in your browser

GET [say/hello](say/hello)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello world!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [say/hello/Restler2.0](say/hello/Restler2.0)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello Restler2.0!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [say/hello?to=R.Arul%20Kumaran](say/hello?to=R.Arul%20Kumaran)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello R.Arul Kumaran!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


If the above links fail, it could be due to missing `.htaccess` file or URL Rewriting is not supported in your server. 
Try the following links instead

GET [index.php/say/hello](index.php/say/hello)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello world!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [index.php/say/hello/Restler2.0](index.php/say/hello/Restler2.0)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello Restler2.0!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [index.php/say/hello?to=R.Arul%20Kumaran](index.php/say/hello?to=R.Arul%20Kumaran)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"Hello R.Arul Kumaran!"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



*[index.php]: _001_helloworld/index.php
*[say.php]: _001_helloworld/say.php
*[restler.php]: ../restler/restler.php
