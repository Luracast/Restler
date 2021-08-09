<?php

namespace Luracast\Restler\OpenApi3\Tags;

use Luracast\Restler\Data\Route;

interface Tagger
{
    public static function tags(Route $route): array;
}
