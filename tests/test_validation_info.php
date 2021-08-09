<?php declare(strict_types=1);

use Luracast\Restler\Data\Param as VI2;

use Luracast\Restler\Utils\CommentParser as CP2;

include __DIR__ . "/../vendor/autoload.php";

$data = ['name' => 'date', 'type' => 'string', 'properties' => ['type' => 'date']];

//var_export(new Param($data));
//var_export(new OldVI($data));

$comment = '/**
     * Date validation
     *
     * @param string $date {@from body}{@type date}
     *
     * @return string {@type date}
     */';

$parsed = CP2::parse($comment);

//$info1 = VI1::__set_state($parsed['param'][0]);
$info2 = VI2::__set_state($parsed['param'][0]);



var_dump($parsed);