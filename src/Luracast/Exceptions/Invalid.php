<?php

namespace Luracast\Restler\Exceptions;

/**
 * Invalid Parameter Exception
 *
 */
class Invalid extends HttpException
{
    public function __construct(?string $errorMessage = null)
    {
        parent::__construct(400, $errorMessage);
    }
}