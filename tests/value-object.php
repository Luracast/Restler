<?php

declare(strict_types=1);

use Luracast\Restler\Data\Param;
use Luracast\Restler\Data\ValueObject;

include __DIR__ . "/../vendor/autoload.php";


class Sample extends ValueObject
{
    public $name;
    private $_age;

    public function getAge()
    {
        return $this->_age;
    }

    public function setAge(int $age)
    {
        $this->_age = $age;
    }

    public function __get($name)
    {
        if (property_exists($this, "_$name")) {
            return $this->{"_$name"};
        }
    }
}


$sample = new Sample();

$sample->name = 'Arul';
$sample->setAge(12);

var_dump($sample->age);

echo json_encode($sample, JSON_PRETTY_PRINT) . PHP_EOL;

//Param::UnSaidUndefined();

echo Param::multipleNullableFloat() . PHP_EOL;
echo Param::nullableFloatArray() . PHP_EOL;
echo json_encode(Param::nullableObject('Mermaid', ['soap' => 'int']), JSON_PRETTY_PRINT) . PHP_EOL;

