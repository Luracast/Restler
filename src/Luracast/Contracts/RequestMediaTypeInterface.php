<?php

namespace Luracast\Restler\Contracts;

interface RequestMediaTypeInterface extends MediaTypeInterface
{
    public function decode(string $data);
}
