<?php
class Math
{
    public $restler;
    function add ($n1 = 1, $n2 = 1)
    {
        $this->_validate(func_get_args());
        return $n1 + $n2;
    }
    function multiply ($n1 = 5, $n2 = 2)
    {
        $this->_validate(func_get_args());
        return array('result' => ($n1 * $n2));
    }
    private function _validate ($numbers)
    {
        foreach ($numbers as $n) {
            if (! is_numeric($n)) {
                throw new RestException(400, 'parameter is not a number');
            }
            if (is_infinite($n)) {
                throw new RestException(400, 'parameter is not finite');
            }
            if ($n > 5000) {
                throw new RestException(416);
            }
        }
    }
}