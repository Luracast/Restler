<?php

class Api
{
    /**
     * Auto routed method which maps to POST api/method/{param1}
     *
     * @param int       $param1 map to url
     * @param array     $param2 map to request body
     * @param string    $param3 map to query string
     *
     * @return string
     */
    public function postMethod($param1, array $param2, $param3 = 'optional')
    {
        return 'you have called Api::postMethod()';
    }

    /**
     * Auto routed method that creates all possible routes
     * this was the standard behavior
     *
     * @param        $p1 {@validate false}
     * @param array  $p2 {@validate true} {@fix false}
     * @param string $p3
     *
     * @return bool|string
     * @smart-auto-routing true
     */
    public function soManyWays($p1, array $p2, $p3 = 'optional')
    {
        return 'you have called Api::soManyWays()';
    }

    /**
     * Manually routed method. we can specify as many routes as we want
     *
     * @url POST method2
     * @url POST method2/{anything}
     * @url GET what/ever/you/want
     */
    public function whatEver($anything)
    {
        return 'you have called Api::whatEver()';
    }
}

