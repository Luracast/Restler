<?php
/*
 Title: Multi-format
 Tagline: Serving what the client wants
 Tags: json, xml
 Requires: PHP >= 5.3
 Description: This BMI calculator service shows how you can serve data in different
 formats using Restler. This example uses JsonFormat (default) and XmlFormat.

 First  format specified in `Restler::setSupportedFormats` is used as the
 default format when client does not specify the format.

 Client can specify the format either using  extension like .json or specify
 the MIME type in HTTP Accept Header.

 When we make the request from the browser we will get xml when we
 skip the extension because XML is one of the requested formats specified in
 the HTTP Accept Header where as a AJAX request or CURL will return JSON.

 Example 1: GET bmi returns

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
</response>

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
*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->setSupportedFormats('JsonFormat', 'XmlFormat');
$r->addAPIClass('BMI');
$r->handle();

