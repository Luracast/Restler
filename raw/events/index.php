<?php
require_once '../../vendor/restler.php';

use Luracast\Restler\Events;

Events::listen('onStart', function($name)
{
    echo "onStart fired for $name!";
},'ve');

Class Observer implements \Luracast\Restler\iObserve{

}
print_r(Events::observe(new Observer()));
Events::trigger('onStart', array('name' => 'Arul'), '\Luracast\Restler\iObserve');

