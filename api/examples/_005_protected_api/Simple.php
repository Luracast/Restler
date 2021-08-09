<?php

class Simple
{
    function normal()
    {
        return 'open for all';
    }

    protected function restricted()
    {
        return 'protected method';
    }

    /**
     * @access protected
     */
    function restricted2()
    {
        return 'protected by comment';
    }
}