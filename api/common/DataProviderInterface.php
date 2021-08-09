<?php

interface DataProviderInterface
{
    function __construct(string $name);

    static function reset();

    function get($id);

    function getAll();

    function insert($rec);

    function update($id, $rec, $create = true);

    function delete($id);
}