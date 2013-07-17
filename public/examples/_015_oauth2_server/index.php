<?php
//date_default_timezone_set('Asia/Singapore');
require_once "../../../vendor/restler.php";
require_once "OAuth2/Server.php";
use Luracast\Restler\Restler;
use OAuth2\Server;

$r = new Restler();
$r->addAuthenticationClass('OAuth2\\Server', '');
$r->setOverridingFormats('JsonFormat', 'HtmlFormat', 'UploadFormat');
$r->handle();