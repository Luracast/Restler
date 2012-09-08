<?php
require_once '../../restler3/restler.php';
require_once '../../restler3/debugformat/debugformat.php';
#set autoloader
#do not use spl_autoload_register with out parameter
#it will disable the autoloading of formats
spl_autoload_register('spl_autoload');
$r = new Restler();
$r->setSupportedFormats(
    'DebugFormat',
    'MustacheFormat',
    'JsonFormat',
    'XmlFormat'
);
$r->addAPIClass('BMI');
$r->addAPIClass('Author');
$r->handle();

