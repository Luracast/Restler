<?php

declare(strict_types=1);


use Luracast\Restler\Core;
use Luracast\Restler\Routes;

include __DIR__ . "/../vendor/autoload.php";

print_r(Routes::scope(new ReflectionClass(Core::class)));
