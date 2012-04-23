<?php
header('Content-Type: text/plain');
echo 'minifying restler php files...'.PHP_EOL;
include_once 'config.php';
minify_all_php(RESTLER_PATH, MINIFIED_PATH, INF);
echo 'done';
function minify_all_php($from_path, $to_path, $recurse_depth=0) {
	#echo "minify_all_php($from_path, $to_path, $recurse_depth)".PHP_EOL;
	if(!is_dir($to_path))mkdir($to_path);
	foreach (glob($from_path.DIRECTORY_SEPARATOR.'*.php') as $filepath) {
		$filename=pathinfo($filepath, PATHINFO_FILENAME).'.php';
		file_put_contents($to_path.DIRECTORY_SEPARATOR.$filename, php_strip_whitespace($filepath));
	}
	if($recurse_depth){
		$recurse_depth--;
		$dirs = array_filter(glob($from_path.DIRECTORY_SEPARATOR.'*'), 'is_dir');
		foreach ($dirs as $dir) {
			$dirname=substr($dir,strlen($from_path));
			#echo $dirname.PHP_EOL;
			minify_all_php($dir,$to_path.$dirname, $recurse_depth);
		}
	}
}