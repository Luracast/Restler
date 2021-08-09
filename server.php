<?php

const WEB_FOLDER = __DIR__ . '/public/';
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (file_exists(WEB_FOLDER . $request) && !is_dir(WEB_FOLDER . $request)) {
    return false; // serve the requested resource as-is.
} else {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PATH_INFO'] = $_SERVER['REQUEST_URI'];
    $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'] . ($_SERVER['REQUEST_URI'] === '/' ? '' : $_SERVER['REQUEST_URI']);
    include WEB_FOLDER . 'index.php';
}
