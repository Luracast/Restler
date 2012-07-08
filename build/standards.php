<?php
header('Content-Type: text/plain');
echo 'checking coding standards complience of restler php files...'.PHP_EOL;
include_once 'config.php';
$command = 'php '.CODE_SNIFFER_PATH.'/scripts/phpcs --standard=PSR --report=full --report-width=80'; //--tab-width=4
$command.= ' -l ';
$command.= RESTLER_PATH;
$output=array();
$return_var = FALSE;
echo PHP_EOL;
echo $command;
echo exec($command, $output, $return_var);
echo implode(PHP_EOL, $output);
