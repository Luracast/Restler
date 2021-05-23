<?php
// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a restler
// application without having installed a "real" web server software here.

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri === '/favicon.ico') return false;
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}
if (file_exists(__DIR__ . '/public' . $uri . 'index.html')) {
    return false;
}
error_reporting(E_ALL ^ E_DEPRECATED);
$dir = dirname(__DIR__ . '/public' . $_SERVER['SCRIPT_NAME']);
while (!is_dir($dir) || !file_exists($target = $dir . '/index.php')) $dir = dirname($dir);
chdir($dir);
if (!file_exists($target)) return false;
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/public';
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PHP_SELF'] = $target;
require $target;
