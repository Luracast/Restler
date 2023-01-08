<?php

namespace Luracast\Restler\Middleware;

use Luracast\Restler\Contracts\ContainerInterface;
use Luracast\Restler\Contracts\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

use function React\Promise\resolve;

class ServerNameFix implements MiddlewareInterface
{
    protected function fixBase(ServerRequestInterface $request): ServerRequestInterface
    {
        $uri = $request->getUri();
        if ($request->hasHeader('X-Forwarded-Host')) {
            $host = $request->getHeaderLine('X-Forwarded-Host');
        }
        $updated = false;
        if (!empty($host)) {
            $updated = true;
            $uri = $uri->withHost($host);
        }
        if ($request->hasHeader('X-Forwarded-Proto')) {
            $updated = true;
            $scheme = $request->getHeaderLine('X-Forwarded-Proto');
            $port = $scheme == 'https' ? 443 : 80;
            $uri = $uri->withScheme($scheme)->withPort($port);
        }
        return $updated ? $request->withUri($uri) : $request;
    }

    public function __invoke(
        ServerRequestInterface $request,
        callable $next = null,
        ContainerInterface $container = null
    ) {
        return resolve($next($this->fixBase($request)));
    }
}
