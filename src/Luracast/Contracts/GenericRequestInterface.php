<?php


namespace Luracast\Restler\Contracts;


use Luracast\Restler\Data\Param;

interface GenericRequestInterface extends ValueObjectInterface
{
    public static function requests(string  ...$types): Param;
}
