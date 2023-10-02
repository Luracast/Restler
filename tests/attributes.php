<?php
/*
#[Attribute]
class ReadOnlyProperty
{

}

#[Attribute]
class Property
{
    public function __construct($type, $name)
    {
    }
}
*/
#[ReadOnlyProperty]
#[Property(type: 'function', name: 'Hello')]
function Hello()
{
    return "Hello";
}

function getAttributes(Reflector $reflection)
{
    $attributes = $reflection->getAttributes();
    $result = [];
    foreach ($attributes as $attribute) {
        $result[$attribute->getName()] = $attribute->getArguments();
    }
    return $result;
}

$reflection = new ReflectionFunction("Hello");
print_r(getAttributes($reflection));
