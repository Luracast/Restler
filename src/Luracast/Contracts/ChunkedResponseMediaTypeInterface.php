<?php

namespace Luracast\Restler\Contracts;

use Generator;
use Luracast\Restler\ResponseHeaders;
use Psr\Http\Message\StreamInterface;

interface ChunkedResponseMediaTypeInterface extends ResponseMediaTypeInterface
{
    /**
     * Encode chunked response data (from a Generator) into specific media type
     *
     * @param Generator $data Generator that yields chunks of data
     * @param ResponseHeaders $responseHeaders
     * @param bool $humanReadable
     * @return string|resource|StreamInterface
     */
    public function encodeChunks(Generator $data, ResponseHeaders $responseHeaders, bool $humanReadable = false);
}
