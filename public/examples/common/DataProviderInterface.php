<?php

interface DataProviderInterface
{
    function get($id);

    function getAll();

    function insert($rec);

    function update($id, $rec);

    function delete($id);

    function reset();
}
