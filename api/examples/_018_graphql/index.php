<?php

use Luracast\Restler\Restler;

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/routes.php';

(new Restler)->handle();
