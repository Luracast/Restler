Luracast Restler 2.0
====================

Restler is a simple and effective multi-protocol REST API Server written in PHP. 
Just deal with your business logic in php, restler will take care of the REST!

* [Developer Home](http://luracast.com/products/restler/)
* [Live Examples](http://bit.ly/RestlerLiveExamples)
* [Taking Care of the REST - Presentation](http://bit.ly/TakingCareOfTheREST)
* [Open Sourced Real World Example - SingMood](http://bit.ly/SingMood)
* [Updates on Twitter](http://twitter.com/Luracast)

Features
--------

* Light weight
* Flexible
* Customizable
* Supports HTTP request methods  GET, POST, PUT, and DELETE
* Clients can use X-HTTP-Method-Override header
* Two way format conversion
* Pluggable Formatters
* Comes with JSON, XML, Yaml, Amf, and Plist(both XML and Binary) formats
* Pluggable Authentication schemes
* Comes with many [Examples](http://bit.ly/RestlerLiveExamples) 
  that can be tried on your localhost to get started
* URL to Method mapping
* URL part to Method parameter mapping
* Supports URLEncoded format for simplified input
* Query parameters to Method parameter mapping
* Source code distributed under LGPL

Changes from Restler 1.0
------------------------

Restler 2.0 is a major rewrite to use convention over configuration and it is optimized 
for performance. Here are some of the major changes and improvements

* PHPDoc comments to map a method to URI is now optional.
* All public methods that does not begin with an underscore are mapped 
  automatically to the method name (`gateway\classname\methodname\param1\...`)
* If we do not specify the second parameter for `$restler->addAPIClass` it will be mapped to the 
  class name instead of mapping it to the root
* Restler 2 is written for PHP 5.3 and above but it make use of compat.php and work on 
  any version of PHP starting from PHP 5.0

more information is available on the
[features page](http://luracast.com/products/restler/features/)