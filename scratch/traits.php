<?php declare(strict_types=1);

use Luracast\Restler\Contracts\UsesAuthenticationInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

include __DIR__ . "/../vendor/autoload.php";

UriInterface::class;
ServerRequestInterface::class;
ResponseInterface::class;

trait Authentication
{
    private $authenticated = false;

    public function __setAuthenticationStatus(bool $isAuthenticated = false, bool $isAuthFinished = false)
    {
        $this->authenticated = $isAuthenticated;
    }
}

class A implements UsesAuthenticationInterface
{
    use Authentication;

    public function __construct(bool $authenticated = false)
    {
        $this->authenticated = $authenticated;
    }
}

class B
{
    use Authentication;

    public function __construct(A $a)
    {
        var_dump($a->authenticated);
    }
}

$a = new A(true);

$b = new B($a);

//PHP Fatal error:  Uncaught Error: Cannot access protected property A::$authenticated in /Users/Arul/Projects/reactphp-restler/public/traits.php:26

