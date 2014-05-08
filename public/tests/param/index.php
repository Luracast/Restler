<?php
/*
 * Testing all the attributes for @param annotation
 */

use Luracast\Restler\Restler;
use Luracast\Restler\Defaults;

require_once "../../../vendor/restler.php";

Defaults::$crossOriginResourceSharing = true;

$r = new Restler();
$r->addAPIClass('MinMax');
$r->addAPIClass('MinMaxFix');
$r->addAPIClass('Type');
$r->addAPIClass('Validation');
$r->addAPIClass('Resources');
$r->handle();