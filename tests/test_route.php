<?php declare(strict_types=1);

use Luracast\Restler\Data\Route;
use Luracast\Restler\Data\Param;
use Luracast\Restler\Utils\CommentParser;

include __DIR__ . "/../vendor/autoload.php";

/**
 * @param int $a {@min 3}{@max 5}
 * @param int $b
 * @return float|int
 */
$action = function (int $a, int $b) {
    return $a / $b;
};

$f = new ReflectionFunction($action);
$c = CommentParser::parse($f->getDocComment());
//var_dump($c);

$route = Route::parse(['metadata' => $c]);
$route->action = $action;
/*

$route = new Route();
$route->action = $action;
$ps = $f->getParameters();
foreach ($ps as $p) {
    $route->addParameter(Param::parse([
        'name' => $p->name,
        'type' => $p->hasType() ? $p->getType()->getName() : null
    ]));
}
*/

/*
$route = new Route();
$route->action = $action;
$a = new Param();
$a->type = 'int';
$a->name = 'a';
$route->addParameter($a);

$b = new Param();
$b->type = 'int';
$b->name = 'b';
$route->addParameter($b);
*/
try {
    print_r($route->call([4, 2]));
    echo PHP_EOL;

    print_r($route->call(['b' => 4, 'a' => 5]));
    echo PHP_EOL;

    print_r($route->call([4, 'b' => 2]));
    echo PHP_EOL;

    print_r($route->call([4, 'b' => 'asas']));
    echo PHP_EOL;
} catch (Throwable $t) {
    echo $t->getMessage();
    echo PHP_EOL;
}
