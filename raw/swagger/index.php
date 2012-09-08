<?php
/*
//redirect to https
if($_SERVER['HTTPS']!="on")
{
    $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header("Location:$redirect");
    exit;
}
*/
use Luracast\Restler\Restler;
require_once '../../vendor/restler.php';

$r = new Restler();//TRUE, TRUE);
$r->addAuthenticationClass('KeyAuth');
//$r->setSupportedFormats('WadlFormat');
//$r->setSupportedFormats('XmlFormat', 'WadlFormat');
$r->setSupportedFormats('DebugFormat', 'JsonFormat');
//$r->addAPIClass('Resources');
$r->addAPIClass('Luracast\Restler\Resources');
//$r->addAPIClass('BMI','weight-management/bmi');
//$r->addAPIClass('BMI');
$r->addAPIClass('Authors');
$r->addAPIClass('Simple');
$r->handle();

