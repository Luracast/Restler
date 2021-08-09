<?php

namespace Luracast\Restler\Contracts;


interface StreamingRequestMediaTypeInterface extends RequestMediaTypeInterface
{
    /**
     * @param $resource resource for a data stream
     *
     * @return array {@type associative}
     */
    public function streamDecode($resource): array;
}
