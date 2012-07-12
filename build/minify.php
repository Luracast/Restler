<?php
header ( 'Content-Type: text/plain' );
echo 'minifying restler php files...' . PHP_EOL;
include_once 'config.php';
minify_all_php ( RESTLER_PATH, MINIFIED_PATH, INF );
echo 'done';

function minify_all_php($from_path, $to_path, $recurse_depth = 0)
{
    // cho "minify_all_php($from_path, $to_path, $recurse_depth)".PHP_EOL;
    if (! is_dir ( $to_path )) {
        mkdir ( $to_path );
    }
    $together = array (
            'restler.php',
            'restexception.php',
            'docparser.php',
            'restlerhelper.php',
            'restlerautoloader.php',
            'iauthenticate.php',
            'iformat.php',
            'irespond.php',
            'urlencodedformat.php',
            'jsonformat.php',
            'defaultresponder.php' 
    );
    $pack = array_fill ( 0, count ( $together ), '' );
    foreach ( glob ( $from_path . DIRECTORY_SEPARATOR . '*.php' ) as $filepath ) {
        $filename = pathinfo ( $filepath, PATHINFO_FILENAME ) . '.php';
        if (in_array ( $filename, $together )) {
            // echo array_search ( $filename, $together ) . " $filename \n";
            $pack [array_search ( $filename, $together )] = php_strip_whitespace ( $filepath );
        } else {
            file_put_contents ( $to_path . DIRECTORY_SEPARATOR . $filename, php_strip_whitespace ( $filepath ) );
        }
    }
    $pack = implode ( '', $pack );
    if (! empty ( $pack )) {
        $pack = str_replace ( '<?php', '', $pack );
        file_put_contents ( $to_path . DIRECTORY_SEPARATOR . 'restler.php', '<?php' . $pack );
    }
    if ($recurse_depth) {
        $recurse_depth --;
        $dirs = array_filter ( glob ( $from_path . DIRECTORY_SEPARATOR . '*' ), 'is_dir' );
        foreach ( $dirs as $dir ) {
            $dirname = substr ( $dir, strlen ( $from_path ) );
            // cho $dirname.PHP_EOL;
            minify_all_php ( $dir, $to_path . $dirname, $recurse_depth );
        }
    }
}