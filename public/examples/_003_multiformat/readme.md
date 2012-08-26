Multi-format <requires>PHP >= 5.3</requires>
------------

<tag>json</tag>
<tag>xml</tag>

This BMI calculator service shows how you can serve data in different
 formats using Restler. This example uses JsonFormat (default) and XmlFormat.

 First  format specified in `Restler::setSupportedFormats` is used as the
 default format when client does not specify the format.

 Client can specify the format either using  extension like .json or specify
 the MIME type in HTTP Accept Header.

 When we make the request from the browser we will get xml when we
 skip the extension because XML is one of the requested formats specified in
 the HTTP Accept Header where as a AJAX request or CURL will return JSON.
> This API Server is made using the following php files/folders

> * index.php      (gateway)
> * BMI.php      (api)
> * restler.php      (framework)

This API Server exposes the following URIs

	GET bmi ⇠ BMI::index()


Try the following links in your browser

GET [bmi](index.php/bmi)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
<?xml version="1.0"?>
<response>
  <bmi>31.77</bmi>
  <message>Obesity</message>
  <metric>
    <height>162.6 centimeter</height>
    <weight>84 kilograms</weight>
  </metric>
  <imperial>
    <height>5 feet 4 inches</height>
    <weight>185.19 pounds</weight>
  </imperial>
</response>
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [bmi.xml](index.php/bmi.xml)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
<?xml version="1.0"?>
<response>
  <bmi>31.77</bmi>
  <message>Obesity</message>
  <metric>
    <height>162.6 centimeter</height>
    <weight>84 kilograms</weight>
  </metric>
  <imperial>
    <height>5 feet 4 inches</height>
    <weight>185.19 pounds</weight>
  </imperial>
</response>
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [bmi.json](index.php/bmi.json)
:	
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "bmi": 31.77,
  "message": "Obesity",
  "metric": {
    "height": "162.6 centimeter",
    "weight": "84 kilograms"
  },
  "imperial": {
    "height": "5 feet 4 inches",
    "weight": "185.19 pounds"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~






*[index.php]: _003_multiformat/index.php
*[BMI.php]: _003_multiformat/BMI.php
*[restler.php]: ../restler/restler.php

