<?php
namespace Luracast\Restler\Exceptions;


use Psr\Container\NotFoundExceptionInterface;

class NotFound extends HttpException implements NotFoundExceptionInterface
{
    public function __construct(?string $errorMessage = null, array $details = array(), $previous = null)
    {
        parent::__construct(404, $errorMessage, $details, $previous);
    }
}
