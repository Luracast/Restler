<?php

namespace Luracast\Restler\Contracts;


use Luracast\Restler\Exceptions\HttpException;

interface ComposerInterface
{
    /**
     * Used by Explorer to
     * @param int $httpStatus http status code {@example 404}
     * @param string $mediaType
     * @return string
     */
    public static function errorResponseClass(int $httpStatus, string $mediaType): string;

    /**
     * Result of an api call is passed to this method
     * to create a standard structure for the data
     *
     * @param mixed $result can be a primitive or array or object
     *
     * @return mixed
     */
    public function response($result);

    /**
     * When the api call results in HttpException this method
     * will be called to return the error message
     *
     * @param HttpException $exception exception that has reasons for failure
     *
     * @return mixed
     */
    public function message(HttpException $exception);
}
