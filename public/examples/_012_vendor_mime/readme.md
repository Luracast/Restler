## Vendor MIME 

 This example requires `PHP >= 5.3` and taggeed under `versioning` `mime` `mediatype` `vendor` `custom`


This example shows how to use vendor specific media types for versioning

###Vendor-Specific Media Types

> Media types use the subtype prefix `vnd` to indicate that they are owned and
> controlled by a `vendor`. Vendor-specific media types convey a clear description
> of a message's content to the programs that understand their meaning.
- [REST API Design Rulebook](http://books.google.com.sg/books?id=eABpzyTcJNIC&lpg=PA40&ots=vxTC21e8JB&dq=vendor%20specific%20media%20types&pg=PA40#v=onepage&q=vendor%20specific%20media%20types&f=false)

Important steps are

```php
Defaults::$apiVendor = "SomeVendor";
Defaults::$useVendorMIMEVersioning = true;

$r = new Restler();
$r->setAPIVersion(2);
//...
```

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * v1\BMI.php      (api)
> * v2\BMI.php      (api)
> * Resources.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET bmi                    ⇠ SomeVendor\v2\BMI::index()
    GET resources              ⇠ Luracast\Restler\Resources::index()
    GET resources/verifyaccess ⇠ Luracast\Restler\Resources::verifyAccess()
    GET resources/{id}         ⇠ Luracast\Restler\Resources::get()


Here is how you will consume different versions of the api using cURL.

**Version 1**

```bash
curl 'http://restler3.luracast.com/examples/_012_vendor_mime/index.php/bmi?height=162.6&weight=84' -H 'Accept: application/vnd.somevendor-v1+json' -i

HTTP/1.1 200 OK
Date: Sun, 06 Jan 2013 10:46:40 GMT
Server: Apache/2.2.22 (Unix) DAV/2 PHP/5.3.14 mod_ssl/2.2.22 OpenSSL/0.9.8o
X-Powered-By: Luracast Restler v3.0.0rc3
Set-Cookie: ZDEDebuggerPresent=php,phtml,php3; path=/
Vary: Accept
Cache-Control: no-cache, must-revalidate
Expires: 0
Content-Language: en
Content-Length: 209
Content-Type: application/vnd.SomeVendor-v1+json; charset=utf-8

{
  "bmi": 31.77,
  "message": "Obesity",
  "metric": {
    "height": "162.6 centimeters",
    "weight": "84 kilograms"
  },
  "imperial": {
    "height": "5 feet 4 inches",
    "weight": "185.19 pounds"
  }
}

```
**Version 2 without unit**
```bash
curl 'http://restler3.luracast.com/examples/_012_vendor_mime/index.php/bmi?height=162.6&weight=84' -H 'Accept: application/vnd.somevendor-v2+json' -i

HTTP/1.1 400 Bad Request
Date: Sun, 06 Jan 2013 10:48:19 GMT
Server: Apache/2.2.22 (Unix) DAV/2 PHP/5.3.14 mod_ssl/2.2.22 OpenSSL/0.9.8o
X-Powered-By: Luracast Restler v3.0.0rc3
Set-Cookie: ZDEDebuggerPresent=php,phtml,php3; path=/
Vary: Accept
Cache-Control: no-cache, must-revalidate
Expires: 0
Content-Language: en
Content-Length: 87
Connection: close
Content-Type: application/vnd.SomeVendor-v2+json; charset=utf-8

{
  "error": {
    "code": 400,
    "message": "Bad Request: invalid height unit"
  }
}

```
**Version 2 with unit**
```bash
curl 'http://restler3.luracast.com/examples/_012_vendor_mime/index.php/bmi?height=1.626meters&weight=84kilograms' -H 'Accept: application/vnd.somevendor-v2+json' -i

HTTP/1.1 200 OK
Date: Sun, 06 Jan 2013 11:05:26 GMT
Server: Apache/2.2.22 (Unix) DAV/2 PHP/5.3.14 mod_ssl/2.2.22 OpenSSL/0.9.8o
X-Powered-By: Luracast Restler v3.0.0rc3
Set-Cookie: ZDEDebuggerPresent=php,phtml,php3; path=/
Vary: Accept
Cache-Control: no-cache, must-revalidate
Expires: 0
Content-Language: en
Content-Length: 209
Content-Type: application/vnd.SomeVendor-v2+json; charset=utf-8

{
  "bmi": 31.77,
  "message": "Obesity",
  "metric": {
    "height": "162.6 centimeters",
    "weight": "84 kilograms"
  },
  "imperial": {
    "height": "5 feet 4 inches",
    "weight": "185.19 pounds"
  }
}

```





*[index.php]: _012_vendor_mime/index.php
*[v1\BMI.php]: _012_vendor_mime/SomeVendor/v1/BMI.php
*[v2\BMI.php]: _012_vendor_mime/SomeVendor/v2/BMI.php
*[Resources.php]: ../../vendor/Luracast/Restler/Resources.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

