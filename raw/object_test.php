<?php

use Luracast\Restler\Data\Object;

require '../vendor/restler.php';
Object::$fix = array();
Object::$separatorChar = '|'; // change

$before = array(
    'my.super.user' => 'arul',
    'password' => 'secret',
    'id' => '54',
    'active' => 1,
    'boolAsString' => 'true',
    'empty1' => null,
    'empty2' => array(),
    'empty3' => '',
);


$f = new \Luracast\Restler\Format\JsonFormat();

echo 'before' . PHP_EOL . $f->encode($before, true);
/*
before
{
  "my.super.user": "arul",
  "password": "secret",
  "id": "54",
  "active": 1,
  "boolAsString": "true",
  "empty1": null,
  "empty2": [

  ],
  "empty3": ""
}
 */
echo PHP_EOL . '-------------------------------------' . PHP_EOL;
Object::$separatorChar = '.'; //promote dotted property as sub object
Object::$fix['id'] = 'intval'; // convert to int
Object::$fix['active'] = 'boolval'; //convert to boolean
Object::$fix['boolAsString'] = 'wincheck'; //string true or false to boolean
Object::$fix['password'] = null; // remove all passwords
Object::$removeEmpty = true;
function boolval($v)
{
    return (bool)$v;
}

function wincheck($value)
{
    return $value !== 'false' && (bool)$value;
}

echo 'after' . PHP_EOL . $f->encode($before, true);
/*
after
{
  "my": {
    "super": {
      "user": "arul"
    }
  },
  "id": 54,
  "active": true,
  "boolAsString": true
}
 */