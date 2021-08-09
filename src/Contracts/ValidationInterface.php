<?php

namespace Luracast\Restler\Contracts;

use Luracast\Restler\Data\Param;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\RestException;

/**
 * Validation classes should implement this interface
 */
interface ValidationInterface
{

    /**
     * method used for validation.
     *
     * @param mixed $input
     *            data that needs to be validated
     * @param Param $param
     *            information to be used for validation
     * @return bool|mixed false in case of failure or fixed value in the expected
     *         type
     * @throws HttpException 400 with information about the
     * failed
     * validation
     */
    public static function validate($input, Param $param);
}

