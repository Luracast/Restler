<?php

class Math
{
    /**
     * @mutation addIntegers
     *
     * @param int $n1
     * @param int $n2
     *
     * @return int
     */
    function add($n1 = 1, $n2 = 1)
    {
        return $n1 + $n2;
    }

    /**
     * @param int $n1 {@from path}
     * @param int $n2 {@from path}
     *
     * @return array
     */
    function multiply($n1, $n2)
    {
        return [
            'result' => ($n1 * $n2)
        ];
    }

    /**
     * @url GET sum/*
     */
    function _sum()
    {
        return array_sum(func_get_args());
    }

    /**
     * @param int ...$numbers {@from path}
     * @return int
     */
    function sum2(int ...$numbers):int
    {
        return array_sum($numbers);
    }
}
