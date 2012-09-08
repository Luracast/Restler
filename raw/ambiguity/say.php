<?php
class Say {
    /**
     * @allowAmbiguity
     */
    function hello($to='world') {
        trigger_error("what?");
        trace('Nice');
        return "Hello $to!";
    }
    /*
    function something(){
        return 'Something';
    }
    */
}

