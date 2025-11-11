# Compose

## What does it do?

`iCompose` interface is the answer to the following questions:

- What should restler return for an api call?
- Exactly what a particular api method returns or wrap it in some default properties?
- How should we convert an exception thrown into a clean error response?
- Should we include the debug information along with the error response? how much and how?

## How does it work?

An implementation of iCompose interface is used to compose both success and error responses with two different methods:

- response method gets the return value of the api method, which can be returned directly or wrapped in a standard
  structure
- exception thrown is sent to the message method, which can format the exception into a error response

```php
namespace Luracast\Restler;

interface iCompose {
    /**
     * @param mixed $result can be a primitive or array or object
     */
    public function response($result);

    public function message(RestException $exception);
}
```

## How to customize

There is a default implementation of this interface named `Compose` returns the success response as such, it formats the
error message as follows

if your exception is as follows

```php
throw new RestException(
  400, 
  'invalid user', 
  ['error_code' => 12002]
);
```

The error response in production mode is

```json
{
  "error": {
    "code": 400,
    "message": "Bad Request: invalid user",
    "error_code": 12002
  }
}
```

*Note:-* the third parameter of RestException is an array of properties which is appended to the response

Additional debug information is returned when restler is running in debug mode. It can be turned off by
using `Compose::$includeDebugInfo=false;` otherwise the response will be

```php
{
  "error": {
    "code": 400,
    "message": "Bad Request: invalid user",
    "error_code": 12002
  },
  "debug": {
    "source": "VerifyUser.php:115 at validate stage",
    "stages": {
      "success": [
        "get",
        "route",
        "negotiate",
        "validate"
      ],
      "failure": [
        "call",
        "message"
      ]
    }
  }
}
```

## Taking full control

You can replace the default iCompose implementation with the following configuration

```php
Luracast\Restler\Defaults::$composeClass = 'MyOwnCompose';
```

Take a look at the `Luracast\Restler\Compose` class. Ideally just copy and paste the code from that
and make your changes.

For example, your response method could be the following to provide a success property and data property.

```php
class MyOwnCompose {
    public function response($result)
    {
        return ['success' => true, 'data' => $result];
    }
    //...
}
```
