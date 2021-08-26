<?php


namespace Luracast\Restler\Contracts;


use JsonSerializable;
use Luracast\Restler\Data\Returns;

interface GenericResponseInterface extends JsonSerializable
{
    public static function responds(string  ...$types): Returns;
}
