<?php declare(strict_types=1);

use Luracast\Restler\Defaults;
use Luracast\Restler\Session;
use Luracast\Restler\Session\FileSessionHandler;

include __DIR__ . "/../api/bootstrap.php";


$handler = new FileSessionHandler(Defaults::$cacheDirectory.'/sessions');
//$handler->open(dirname(__DIR__) . '/api/common/store', 'main');

$session = new Session($handler, $handler, '4b9761767de3abb95ae3ce2932918ac1779490d135cae43a06a2fe1e5ad32873');

//$session->regenerateId();

if ($session->status() !== PHP_SESSION_ACTIVE) {
    $session->start();
}
if (!$session->hasFlash('message')) {
    $session->set('name', 'arul');
    $session->set('age', 44);
    $session->setFlash('message','order dispatched successfully');
}
$session->save();


var_dump($session->get('name'));
foreach ($session as $key => $value) {
    echo "$key = $value\n";
}
//$handler->write('session01', serialize(['name' => 'arul', 'age' => 44]));
