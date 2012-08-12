<?php
class Custom {
    public $one = 1;
    public $two = 2;
}

/*
$o = new Custom ();
var_export ( $o );
*/
header('ContentType: text/plain');
$data  = array(
'one' => 1,
'two' => 2,
);
$o = Custom::__set_state($data);
print_r($o);

