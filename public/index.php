<?php declare(strict_types=1);

use Luracast\Restler\Restler;

require __DIR__ . '/../api/bootstrap.php';

(new Restler)->handle();
