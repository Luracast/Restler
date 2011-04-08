<?php
//SIMPLE USAGE EXAMPLE

//rester and related classes should be
//placed in include path
spl_autoload_register();
$restler = new Restler();
$restler->setSupportedFormats('JsonFormat', 'XmlFormat');
$restler->addAPIClass('SimpleService');
$restler->addAuthenticationClass('DigestAuthentication');
$restler->handle();