<?php


namespace Luracast\Restler\MediaTypes;


use Luracast\Restler\Contracts\ResponseMediaTypeInterface;
use Luracast\Restler\ResponseHeaders;
use Psr\Http\Message\StreamInterface;

class Text extends MediaType implements ResponseMediaTypeInterface
{

    /**
     * Encode the response into specific media type
     * @param array|object $data
     * @param ResponseHeaders $responseHeaders
     * @param bool $humanReadable
     * @return string
     */
    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false): string
    {
        if (!is_string($data) || (is_object($data) && !method_exists($data, '__toString'))) {
            $data = json_encode($data);
        }
        return (string)$data;
    }
}
