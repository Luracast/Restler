<?php
namespace Luracast\Restler;


use Luracast\Restler\Contracts\ComposerInterface;
use Luracast\Restler\Data\ErrorResponse;
use Luracast\Restler\Exceptions\HttpException;

class Composer implements ComposerInterface
{
    /**
     * @var bool When restler is not running in production mode, this value will
     * be checked to include the debug information on error response
     */
    public static bool $includeDebugInfo = true;

    /**
     * Result of an api call is passed to this method
     * to create a standard structure for the data
     *
     * @param mixed $result can be a primitive or array or object
     *
     * @return mixed
     */
    public function response($result)
    {
        return $result;
    }

    /**
     * When the api call results in HttpException this method
     * will be called to return the error message
     *
     * @param HttpException $exception exception that has reasons for failure
     *
     * @return ErrorResponse
     */
    public function message(HttpException $exception): ErrorResponse
    {
        return new ErrorResponse($exception, !Defaults::$productionMode && self::$includeDebugInfo);
    }

    public static function errorResponseClass(int $httpStatus, string $mediaType): string
    {
        return ErrorResponse::class;
    }
}
