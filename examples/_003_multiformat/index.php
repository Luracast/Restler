<?php
/*
 Title: Multi-format.
 Tagline: Serving what the client wants.
 Description: This BMI calculator service shows how you can serve data in different 
 formats using Rester. This example uses JsonFormat (default) and XmlFormat. 
 
 First  format specified in `Restler::setSuportedFormats` is used as the default 
 format when client does not specify the format. 
 
 Client can specify the format either using  extension like .json or specify 
 the MIME type in HTTP Accept Header.
 
 Example 1: GET bmi returns 
 
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
}.

 Example 2: GET bmi.xml returns 
  
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
</response>.

 Example 3: GET bmi.json returns 
 
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
.
 */

require_once '../../restler/restler.php';

#set autoloader
#do not use spl_autoload_register with out parameter
#it will disable the autoloading of formats
spl_autoload_register('spl_autoload');

$r = new Restler();
$r->setSupportedFormats('JsonFormat', 'XmlFormat');
$r->addAPIClass('BMI');
$r->handle();