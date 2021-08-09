<?php
namespace Luracast\Restler\Exceptions;


class InvalidAuthCredentials extends HttpException
{
    public function __construct(?string $errorMessage = 'Invalid Authentication Credentials')
    {
        parent::__construct(401, $errorMessage);
    }
}
