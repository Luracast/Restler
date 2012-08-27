<?php
echo '<pre>';
//print_r($_SERVER);
include_once '../../vendor/restler.php';
echo PHP_VERSION.PHP_EOL;
print_r(get_class_vars('Defaults'));

function setObjectProperty($object, $property, $value)
{
    if (property_exists(get_class($object), $property)) {
        array_key_exists($property, get_object_vars($object))
            ? $object->{$property} = $value
            : $object::$$property = $value;
    }
}
function getObjectProperty($object, $property)
{
    if (property_exists(get_class($object), $property)) {
        return array_key_exists($property, get_object_vars($object))
            ? $object->{$property}
            : $object::$$property;
    }
}

$d = new \Luracast\Restler\Defaults();
setObjectProperty($d, 'headerExpires', 500);
echo getObjectProperty($d,'headerExpires').PHP_EOL;
echo $d::$$headerExpires.PHP_EOL;
print_r(get_class_vars('Defaults'));
