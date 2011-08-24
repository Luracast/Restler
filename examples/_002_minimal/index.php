<?php
/*
 Tagline: Less is more.
 Description: Shows the bare minimum code needed to get your RESTful api server up and running.
 Example 1: GET math/add returns 2.
 Example 2: GET math/add/4/3 returns 7.
 Example 3: GET math/add?n1=6&n2=4 returns 10.
 Example 4: GET math/multiply returns {"result":10}.
 Example 5: GET math/multiply/4/3 returns {"result":12}.
 Example 6: GET math/multiply?n2=4 returns {"result":20}.
 */

#add restler to include path
set_include_path(get_include_path() . PATH_SEPARATOR . '../../restler');

#set autoloader
#do not use spl_autoload_register with out parameter
#it will disable the autoloading of formats
spl_autoload_register('spl_autoload');

$r = new Restler();
$r->addAPIClass('Math');
$r->handle();