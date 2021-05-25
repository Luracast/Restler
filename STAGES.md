# Stages

### Stages of handling a request in restler

When restler receives a request it handles it in the following stages:

1. **get**: Restler identifies request method, url, data and format
2. **route**: Finding out a matching route for the request
3. **negotiate** Applies content negotiation as requested by the api client.
4. **preAuthFilter**: If there is a filer applicable to the route before authentication it is applied.
5. **Authenticate**: If the selected route requires authentication it is applied.
6. **postAuthFilter**: If there is a filer applicable to the route after successful authentication it is applied.
7. **validate**: Applies validation for each and every parameter as defined through the phpdoc comments.
8. **call**: Finally calls the php method with the validated parameters.
9. **message**: Return value of the above call sent as the response in the selected format.

A successful request goes through all the above stages. Whereas a failed request runs until the stage of the failure
and then returns a composed failure message taking the fail fast approach for improved performance.

We can subscribe to any of the following events to listen to them.

| method           | signature                            | description                                   |
|------------------|--------------------------------------|-----------------------------------------------|
| onGet            | onGet(Callable $function)            | fired before reading the request details      |
| onRoute          | onRoute(Callable $function)          | fired before finding the api method           |
| onNegotiate      | onNegotiate(Callable $function)      | fired before content negotiation              |
| onPreAuthFilter  | onPreAuthFilter(Callable $function)  | fired before pre auth filtering               |
| onAuthenticate   | onAuthenticate(Callable $function)   | fired before auth                             |
| onPostAuthFilter | onPostAuthFilter(Callable $function) | fired before post auth filtering onValidate() |
| onValidate       | onValidate(Callable $function)       | fired before validation                       |
| onCall           | onCall(Callable $function)           | fired before api method call                  |
| onCompose        | onCompose(Callable $function)        | fired before composing response               |
| onRespond        | onRespond(Callable $function)        | fired before sending response                 |
| onComplete       | onComplete(Callable $function)       | fired after sending response                  |
| onMessage        | onMessage(Callable $function)        | fired before composing error response         |

These methods are available both statically and dynamically at runtime. so both the following examples are valid.

### Usage Examples:

#### 1. Logging successful api responses

We can use `onComplete` method for logging.

```php
require_once '../vendor/autoload.php';

use Luracast\Restler\Restler;
use Luracast\Restler\User;

$r = new Restler();

$r->onComplete(function () use ($r) {
    $log = array(
        'api'     =>json_encode($r->apiMethodInfo->parameters),
        'ip'      => User::getIpAddress(),
        'route'   => $r->apiMethodInfo->className.'::'.$r->apiMethodInfo->methodName,
        'method'  => $r->requestMethod,
        'parameters' => json_encode($r->apiMethodInfo->parameters)
    );
    print_r($log); //your logging function here!
});

$r->addAPIClass('Say');
$r->handle();
```

#### 2. Lazily setting form style.

```php
require_once '../vendor/autoload.php';

use Luracast\Restler\Restler;
use Luracast\Restler\UI\Forms;
use Luracast\Restler\UI\Foundation5Form;

//no dependency on restler instance so it can be called statically
Restler::onCall(function () { 
    Forms::setStyles(new Foundation5Form());
});

$r = new Restler();
$r->addAPIClass('Something');
$r->handle();
```
