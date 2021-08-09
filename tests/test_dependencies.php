<?php declare(strict_types=1);

use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Utils\ClassName;

include __DIR__ . "/../vendor/autoload.php";

//ClassName::get('ZendAmf\Parser\Amf3\Deserializer');

spl_autoload_register(function ($className) {
    if ($info = ClassName::$dependencies[$className]) {
        throw new HttpException(501, $info[1]
            . ' has external dependency. Please run `composer require ' .
            $info[0] . '` from the project root. Read https://getcomposer.org for more info');
    }
    return false;
});

new ZendAmf\Parser\Amf3\Deserializer();