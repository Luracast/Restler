<?php declare(strict_types=1);

use Luracast\Restler\Data\Param;
use Luracast\Restler\Data\Returns;
use Luracast\Restler\Utils\CommentParser;

include __DIR__ . "/../vendor/autoload.php";

$type = Returns::__set_state(['type' => 'int', 'multiple' => true, 'scalar' => true]);

echo $type . PHP_EOL;

/**
 * Class Test
 */
class Test
{
    /** @var null|array {@type integer|null} an array of integers */
    public array $obj;
    /** @var null|array {@type float} an array of floating point numbers */
    public $arr;

    /**
     * @param null|string $name {@type password}
     * @return string|null
     */
    function welcome(string $name): ?string
    {
        return "welcome $name!";
    }
}

echo ($type = Returns::fromProperty($obj = new ReflectionProperty(Test::class, 'obj'))) . PHP_EOL;
echo ($type = Returns::__set_state($type->jsonSerialize())) . PHP_EOL;
$arr = new ReflectionProperty(Test::class, 'arr');
$doc = CommentParser::parse($arr->getDocComment());
echo ($type = Returns::fromProperty(null, $doc['var'])) . PHP_EOL;


$method = new ReflectionMethod(Test::class, 'welcome');
//var_dump(CommentParser::parse($method->getDocComment()));
print_r(Param::fromMethod($method));

echo ($type = Returns::fromClass(new ReflectionClass(Test::class))) . PHP_EOL;
print_r($type);

/*

$p = new ReflectionProperty(Returns::class, 'format');

$t = $p->getReturns();

var_dump($t->getName());
var_dump($t->isBuiltin());
var_dump($t->allowsNull());
*/
