<?php

namespace Luracast\Restler\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        callable $next = null,
        ContainerInterface $container = null
    );
}