<?php
/*
Title: Versioning
Tagline: using the URL
Tags: versioning
Requires: PHP >= 5.3
Description:
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
method which returns the maximum supported version. Take a look at `Resources`
class for a sample implementation.

Try this example and the version differences in the explorer [here](explorer/index.html#!/v2)

Example 1: GET bmi?height=190 returns

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

Example 2: GET v1/bmi?height=190 returns

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

Example 3: GET v2/bmi?height=190 returns

{
  "error": {
    "code": 400,
    "message": "Bad Request: invalid height unit"
  }
}

Example 4: GET v2/bmi?height=162cm returns

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

Content:

*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Defaults;
use Luracast\Restler\Restler;

Defaults::$useUrlBasedVersioning = true;

$r = new Restler();
$r->setAPIVersion(2);
$r->addAPIClass('BMI');
$r->addAPIClass('Explorer');
$r->handle();