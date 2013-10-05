<?php
/*
 * Testing all the attributes for @param annotation
 */

use Luracast\Restler\Restler;

require_once "../../../vendor/restler.php";

$r = new Restler();
$r->addAPIClass('Data', '');
$r->addAPIClass('Resources');
$r->handle();