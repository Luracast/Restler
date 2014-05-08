## Versioning 

 This example requires `PHP >= 5.3` and taggeed under `versioning`


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

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * v1\BMI.php      (api)
> * v2\BMI.php      (api)
> * Resources.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET bmi                       ⇠ v2\BMI::index()
    GET resources                 ⇠ Luracast\Restler\Resources::index()
    GET resources/verifyaccess    ⇠ Luracast\Restler\Resources::verifyAccess()
    GET resources/{id}            ⇠ Luracast\Restler\Resources::get()
    GET v1/bmi                    ⇠ v1\BMI::index()
    GET v1/resources              ⇠ Luracast\Restler\Resources::index()
    GET v1/resources/verifyaccess ⇠ Luracast\Restler\Resources::verifyAccess()
    GET v1/resources/{id}         ⇠ Luracast\Restler\Resources::get()
    GET v2/bmi                    ⇠ v2\BMI::index()
    GET v2/resources              ⇠ Luracast\Restler\Resources::index()
    GET v2/resources/verifyaccess ⇠ Luracast\Restler\Resources::verifyAccess()
    GET v2/resources/{id}         ⇠ Luracast\Restler\Resources::get()






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





*[index.php]: _011_versioning/index.php
*[v1\BMI.php]: _011_versioning/v1/BMI.php
*[v2\BMI.php]: _011_versioning/v2/BMI.php
*[Resources.php]: ../../vendor/Luracast/Restler/Resources.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

