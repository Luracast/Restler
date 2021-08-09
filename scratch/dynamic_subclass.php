<?php declare(strict_types=1);

use Illuminate\Config\Repository as Config;
use Luracast\Restler\Container;
use Luracast\Restler\Utils\ValidationInfo;
use Luracast\Restler\Defaults;
use Luracast\Restler\Utils\Validator;

include __DIR__ . "/../vendor/autoload.php";

define('BASE', dirname(__DIR__));

$c = new Container();

class A {
    public function __construct(array $defaults)
    {
        var_dump($defaults);
    }
}
$c->make(A::class);
die();

/*

$config = new Config;//(BASE . '/config');

$values = get_class_vars(Defaults::class);

$config['defaults'] = $values;

//$config['defaults.suppressResponseCode']=true;

$get = ['suppress_response_codes' => 'true'];

foreach ($get as $key => $value) {
    if ($alias = $config['defaults.aliases.' . $key]) {
        unset($get[$key]);
        $get[$alias] = $value;
        $key = $alias;
    }
    if (in_array($key, $config['defaults.overridables'])) {
        if (@is_array(Defaults::$validation[$key])) {
            $info = new ValidationInfo(Defaults::$validation[$key]);
            $value = Validator::validate($value, $info);
        }
        $config['defaults.' . $key] = $value;
    }
}

var_export($config['defaults']);

/*
var_export(array_replace_recursive(
    get_class_vars(Defaults::class),
    $config['app']
));



class A
{
    public $var = 'something';
}

class B extends A
{
    public function __set($name, $value)
    {
        var_dump($name, $value);
    }
}

;

$b = new B();

$b->var = 22;
*/