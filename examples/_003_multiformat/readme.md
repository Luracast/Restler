Multi-format
------------

This BMI calculator service shows how you can serve data in different 
formats using Rester. This example uses JsonFormat (default) and XmlFormat. 

First  format specified in `Restler::setSuportedFormats` is used as the default 
format when client does not specify the format. 

Client can specify the format either using  extension like .json or specify 
the MIME type in HTTP Accept Header
> This API Server is made using the following php files/folders

> * index.php      (gateway)
> * bmi.php      (api)
> * restler.php      (framework)
> * xmlformat.php      (format)

This API Server exposes the following URIs

	GET bmi                 ⇠ BMI::index()
	GET bmi/:height         ⇠ BMI::index()
	GET bmi/:height/:weight ⇠ BMI::index()


Try the following links in your browser

GET [bmi](index.php/bmi)
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
*[bmi.php]: _003_multiformat/bmi.php
*[restler.php]: ../restler/restler.php
*[xmlformat.php]: ../restler/xmlformat.php
