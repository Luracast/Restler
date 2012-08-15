<?php
use Luracast\Restler\Restler;
require_once '../../vendor/restler.php';

$r = new Restler();//TRUE, TRUE);
//$r->setSupportedFormats('WadlFormat');
//$r->setSupportedFormats('XmlFormat', 'WadlFormat');
$r->addAPIClass('Resources');
//$r->addAPIClass('BMI','weight-management/bmi');
$r->addAPIClass('BMI');
$r->addAPIClass('Authors');
$r->addAPIClass('Simple');
$r->handle();