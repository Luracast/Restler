<?php

const WEB_FOLDER = __DIR__ . '/public/';
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (file_exists(WEB_FOLDER . $request) && !is_dir(WEB_FOLDER . $request)) {
    return false; // serve the requested resource as-is.
} else {
    $scheme = 'http';
    $host = $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
    $portPos = strpos($host, ":");
    if ($portPos) {
        $port = substr($host, $portPos + 1);
    } else {
        $port = $_SERVER['SERVER_PORT'] ?? '80';
        $port = $_SERVER['HTTP_X_FORWARDED_PORT'] ?? $port; // Amazon ELB
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        if (isset($_SERVER['HTTP_X_FORWARDED_PORT'])) {
            $port = $_SERVER['HTTP_X_FORWARDED_PORT'];
        } elseif (!$portPos) {
            $port = $scheme == 'https' ? '443' : '80';
        }
    }
    $https = $port === '443' || $scheme === 'https' ||
        isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on');
    $_SERVER['HTTPS'] = $https ? 'on' : 'off';
    $_SERVER['SERVER_PORT'] = $port;
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PATH_INFO'] = $_SERVER['REQUEST_URI'];
    $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'] . ($_SERVER['REQUEST_URI'] === '/' ? '' : $_SERVER['REQUEST_URI']);
    include WEB_FOLDER . 'index.php';
}
