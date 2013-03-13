<?php
use Luracast\Restler\Format\HtmlFormat;

require_once('../vendor/restler.php');

$html = new HtmlFormat();

echo $html->encode(array('name'=>'arul'));
