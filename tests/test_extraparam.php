<?php declare(strict_types=1);

use React\Promise\Promise;
use React\Promise\PromisorInterface;

include __DIR__ . "/../vendor/autoload.php";

function name(string $name)
{
    return new React\Promise\Deferred();

}

$value = name('Arul', function () {
});

if (is_a($value, PromisorInterface::class)) {
    echo 'True';
}

//var_dump($value);
echo PHP_EOL;