<?php
namespace Luracast\Restler\MediaTypes;

use Luracast\Restler\Contracts\ResponseMediaTypeInterface;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\ResponseHeaders;

class Js extends MediaType implements ResponseMediaTypeInterface
{
    public const MIME = 'text/javascript';
    public const EXTENSION = 'js';

    public static int $encodeOptions = 0;
    public static string $callbackMethodName = 'parseResponse';
    public static string $callbackOverrideQueryString = 'callback';
    public static bool $includeHeaders = true;

    /**
     * @param $data
     * @param ResponseHeaders $responseHeaders
     * @param bool $humanReadable
     * @return string
     * @throws HttpException
     */
    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false): string
    {
        $r = array();
        if (static::$includeHeaders) {
            $r['meta'] = array();
            foreach (headers_list() as $header) {
                list($h, $v) = explode(': ', $header, 2);
                $r['meta'][$h] = $v;
            }
        }
        $r['data'] = $data;
        if (isset($_GET[static::$callbackOverrideQueryString])) {
            static::$callbackMethodName
                = (string)$_GET[static::$callbackOverrideQueryString];
        }
        $options = static::$encodeOptions;
        if ($humanReadable) {
            $options |= JSON_PRETTY_PRINT;
        }

        $encoded = json_encode($this->convert->toArray($r, true), $options);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new HttpException(500, 'JSON Parser: ' . json_last_error_msg());
        }

        return static::$callbackMethodName . '(' . $encoded . ');';
    }
}
