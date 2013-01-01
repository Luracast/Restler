Multi-format <requires>PHP >= 5.3</requires>
------------
<tag>json</tag> <tag>xml</tag> 

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
> 
> * index.php      (gateway)
> * BMI.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)
> * XmlFormat.php      (format)

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




We expect the following behaviour from this example.

```gherkin

@example3 @multiformat
Feature: Testing Multi-format Example

  Scenario: Default format, when not specified
    When I request "/examples/_003_multiformat/bmi"
    Then the response status code should be 200
    And the response is JSON
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Obesity"

  Scenario: Use XML format when specified as extension
    When I request "/examples/_003_multiformat/bmi.xml"
    Then the response status code should be 200
    And the response is XML
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Obesity"

  Scenario: Correct weight and height should yeild 'Normal weight' as result
    Given that "height" is set to 180
    And "weight" is set to 80
    When I request "/examples/_003_multiformat/bmi.xml{?height,weight}"
    Then the response status code should be 200
    And the "message" property equals "Normal weight"
```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
bin/behat  features/examples/_003_multiformat.feature
```



*[index.php]: _003_multiformat/index.php
*[BMI.php]: _003_multiformat/BMI.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php
*[XmlFormat.php]: ../../vendor/Luracast/Restler/Format/XmlFormat.php

