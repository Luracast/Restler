<?php

namespace Luracast\Restler\Contracts;

use Luracast\Restler\ResponseHeaders;
use Psr\Http\Message\StreamInterface;

interface ResponseMediaTypeInterface extends MediaTypeInterface
{
    /**
     * Encode the response into specific media type
     * @param array|object $data
     * @param ResponseHeaders $responseHeaders
     * @param bool $humanReadable
     * @return string|resource|StreamInterface
     */
    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false);
}
