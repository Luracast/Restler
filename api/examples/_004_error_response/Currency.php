<?php

use Luracast\Restler\Exceptions\HttpException;

class Currency
{
    function format($number = null)
    {
        /**
         * There is a better way to validate in Restler 3
         * Here we manually validate to show the use of exceptions
         */
        if (is_null($number)) {
            throw new HttpException(400);
        }
        if (!is_numeric($number)) {
            throw new HttpException(400, 'not a valid number');
        }

        // let's format it as US currency
        return'$' . number_format($number, 2);
    }
}
