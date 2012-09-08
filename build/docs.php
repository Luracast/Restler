<?php
header ( 'Content-Type: text/plain' );
echo 'building documentation for restler php files...' . PHP_EOL;
include_once 'config.php';

//update path environment variable for graphviz (used for creating flow charts)
putenv ( "PATH=" . getenv('PATH'). ":/opt/local/bin/" );

$command = 'cd '.PROJECT_ROOT.PHP_EOL;
$command .= 'php ' .BUILD_DIR.DIRECTORY_SEPARATOR. PHPDOC_DIR
            . '/bin/phpdoc.php -d ' . RESTLER_DIR;
$command .= ' -t ' . DOCS_DIR ;
$command .= ' --ignore amfformat/,wadlformat/,chartformat/,debugformat/,'
            . 'mustacheformat/,plistformat/,yamlformat/';
$command .= ' --title="' . API_DOC_TITLE . '"';

//file_put_contents('docs.sh', $command);

$output = array ();
$return_var = FALSE;
echo PHP_EOL;
echo $command;
echo PHP_EOL;
echo PHP_EOL;
echo exec ( $command, $output, $return_var );
//echo exec ( 'bash ./docs.sh', $output, $return_var );
echo implode ( PHP_EOL, $output );
echo $return_var;

