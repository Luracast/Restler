<?php
require_once '../../vendor/restler.php';
//use Luracast\Restler\Restler;

$r = new Restler(true, true);
$r->setSupportedFormats('XmlFormat');
$r->addAPIClass('BMI');
$r->handle();