## Versioning 

 This example requires `PHP >= 5.4` and tagged under `versioning`


This example shows how to version your API using namespace. Restler supports
both url based versioning (this example) and vendor specific media type
versioning (next example).

Important steps are

```php
Defaults::$useUrlBasedVersioning = true;

$r = new Restler();
$r->setAPIVersion(2);
//...
```

Only integers are supported for versioning. When not specified explicitly the
version is assumed to be one.

That means when the namespace does not contain
version number as the last part is assumed to be version 1.

Similarly when the API consumer calls the api without the version number he
will receive version 1.

Version number should only be increased when the api signature and or the return
data changes. Use your version control system such as git for all other
versioning needs.

For simplicity we only used the version number as the namespace, but practically
You need to namespace it as `{vendor}\{product}\v{version}`

Which will be `Luracast\WeightManagement\v2` for this example

If a class remains the same across few versions of the api, we can implement
`iProvideMultiVersionApi` interface which is simply defining `__getMaximumSupportedVersion`
method which returns the maximum supported version. Take a look at `Explorer`
class for a sample implementation.

Try this example and the version differences in the explorer [here](explorer/index.html#!/v2)

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * v1\BMI.php      (api)
> * v2\BMI.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET bmi                 ⇠ v2\BMI::index()
    GET explorer/*          ⇠ Luracast\Restler\Explorer\v2\Explorer::get()
    GET explorer/swagger    ⇠ Luracast\Restler\Explorer\v2\Explorer::swagger()
    GET v1/bmi              ⇠ v1\BMI::index()
    GET v1/explorer/*       ⇠ Luracast\Restler\Explorer\v2\Explorer::get()
    GET v1/explorer/swagger ⇠ Luracast\Restler\Explorer\v2\Explorer::swagger()
    GET v2/bmi              ⇠ v2\BMI::index()
    GET v2/explorer/*       ⇠ Luracast\Restler\Explorer\v2\Explorer::get()
    GET v2/explorer/swagger ⇠ Luracast\Restler\Explorer\v2\Explorer::swagger()






Try the following links in your browser

GET [bmi?height=190](index.php/bmi?height=190)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "bmi": 23.27,
  "message": "Normal weight",
  "metric": {
    "height": "190 centimeters",
    "weight": "84 kilograms"
  },
  "imperial": {
    "height": "6 feet 2 inches",
    "weight": "185.19 pounds"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [v1/bmi?height=190](index.php/v1/bmi?height=190)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "bmi": 23.27,
  "message": "Normal weight",
  "metric": {
    "height": "190 centimeters",
    "weight": "84 kilograms"
  },
  "imperial": {
    "height": "6 feet 2 inches",
    "weight": "185.19 pounds"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [v2/bmi?height=190](index.php/v2/bmi?height=190)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "error": {
    "code": 400,
    "message": "Bad Request: invalid height unit"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [v2/bmi?height=162cm](index.php/v2/bmi?height=162cm)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "bmi": 23.27,
  "message": "Normal weight",
  "metric": {
    "height": "190 centimeters",
    "weight": "84 kilograms"
  },
  "imperial": {
    "height": "6 feet 2 inches",
    "weight": "185.19 pounds"
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




We expect the following behaviour from this example.

```gherkin

@example11 @versioning
Feature: Testing Versioning

  Scenario: Access version 1 as default
    When I request "examples/_011_versioning/bmi?height=190"
    Then the response status code should be 200
    And the response is JSON
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Normal weight"
    And the "metric.height" property equals "190 centimeters"

  Scenario: Access version 1 by url
    When I request "examples/_011_versioning/v1/bmi?height=190"
    Then the response status code should be 200
    And the response is JSON
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Normal weight"
    And the "metric.height" property equals "190 centimeters"

  Scenario: Access version 2 by url and passing invalid argument
    When I request "examples/_011_versioning/v2/bmi?height=190"
    Then the response status code should be 400
    And the response is JSON
    And the type is "array"
    And the "error.message" property equals "Bad Request: invalid height unit"

  Scenario: Access version 2 by url
    When I request "examples/_011_versioning/v2/bmi?height=190cm"
    Then the response status code should be 200
    And the response is JSON
    And the type is "array"
    And the response has a "bmi" property
    And the "message" property equals "Normal weight"
    And the "metric.height" property equals "190 centimeters"

```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
vendor/bin/behat  features/examples/_011_versioning.feature
```



*[index.php]: _011_versioning/index.php
*[v1\BMI.php]: Restler-v1/public/examples/_011_versioning/v1/BMI.php
*[v2\BMI.php]: Restler-v2/public/examples/_011_versioning/v2/BMI.php
*[restler.php]: ../../restler.php
*[JsonFormat.php]: ../../src/Format/JsonFormat.php

