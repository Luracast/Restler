<?php
//date_default_timezone_set('Asia/Singapore');
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;
use OAuth2\Client;

//This client takes to the server in the next folder, you can change it by un commenting
//Client::$serverUrl = 'http://brentertainment.com/oauth2/lockdin';

$r = new Restler();
$r->addAPIClass('OAuth2\\Client', '');
$r->setOverridingFormats('HtmlFormat','UploadFormat','JsonFormat');
$r->handle();