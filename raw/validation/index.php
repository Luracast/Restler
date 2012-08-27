<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
/*
class A {
function b(B $c, array $d, $e) {
}
}
class B {
}

$refl = new ReflectionClass('A');
$par = $refl->getMethod('b')->getParameters();

var_dump($par[0]->getClass()->getName());  // outputs B
var_dump($par[1]->getClass());  // note that array type outputs NULL
var_dump($par[2]->getClass());  // outputs NULL
require_once '../../restler/restler.php';
require_once '../../restler/iformat.php';
require_once '../../restler/debugformat/debugformat.php';
require_once '../../restler/jsonserializable.php';
require_once '../../restler/ivalueobject.php';
require_once '../../restler/valueobject.php';
 */

require_once '../../vendor/restler.php';
use Luracast\Restler\Restler;
//echo "<pre> RESTLER version ".Restler::VERSION;
//require_once 'AnotherCustom.php';
//print_r(get_included_files());
//print_r(get_declared_classes());
$r = new Restler(true, true);
$r->addAPIClass('Validate');
$r->handle();
//echo "RESTLER version ".Restler::VERSION;
//$r->setSupportedFormats('DebugFormat', 'JsonFormat');
/*
*/