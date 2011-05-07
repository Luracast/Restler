<?php
//SIMPLE USAGE EXAMPLE

//rester and related classes should be
//placed in include path
set_include_path(get_include_path() . PATH_SEPARATOR . '../../restler');

spl_autoload_register();
$restler = new Restler();
$restler->setSupportedFormats('JsonFormat', 'XmlFormat');
$restler->addAPIClass('SimpleService');
$restler->addAuthenticationClass('DigestAuthentication');
$restler->handle();