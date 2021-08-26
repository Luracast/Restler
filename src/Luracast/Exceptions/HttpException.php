<?php
namespace Luracast\Restler\Exceptions;

use Exception;
use Throwable;

class HttpException extends Exception
{
    /**
     * HTTP status codes
     */
    public static array $codes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        429 => 'Too Many Requests', //still in draft but used for rate limiting
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ];
    public bool $emptyMessageBody = false;
    private array $details = [];
    private $headers = [];

    /**
     * HttpException constructor.
     * @param int $httpStatusCode
     * @param null|string $errorMessage
     * @param array $details
     * @param ?Throwable $previous
     */
    public function __construct(
        int $httpStatusCode = 500,
        ?string $errorMessage = null,
        array $details = [],
        ?Throwable $previous = null
    ) {
        $errorMessage ??= static::$codes[$httpStatusCode] ?? null;
        $this->details = $details;
        parent::__construct($errorMessage, $httpStatusCode, $previous);
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function getErrorMessage()
    {
        $statusCode = $this->getCode();
        $message = $this->getMessage();
        if (empty($message) && isset(static::$codes[$statusCode])) {
            $message = static::$codes[$statusCode];
        }
        return $message;
    }

    public function getSource()
    {
        $e = $this;
        while ($e->getPrevious()) {
            $e = $e->getPrevious();
        }
        return basename($e->getFile()) . ':'
            . $e->getLine();
    }
}
