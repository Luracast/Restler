<?php
$var = 'one ';

$pid = pcntl_fork();

/*
 * From this point on, the process has been forked (or
 * $pid will be -1 in case of failure.)
 *
 * $pid will be a different value in parent & child process:
 * * in parent: $pid will be the process id of the child
 * * in child: $pid will be 0 (zero)
 *
 * We can define 2 separate code paths for both processes,
 * using $pid.
 */
function callback(string $value)
{
    echo "callback $value\n";
}

if ($pid === -1) {
    exit; // failed to fork
} elseif ($pid === 0) {
    // $pid = 0, this is the child thread

    /*
     * Existing variables will live in both processes,
     * but changes will not affect other process.
     */
    echo $var; // will output 'one'
    $var = 'two'; // will not affect parent process
    callback('from child ' . $var);
    /* Do some work */
} else {
    // $pid != 0, this is the parent thread

    /*
     * Do some work, while already doing other
     * work in the child process.
     */

    echo $var; // will output 'one'
    $var = 'three'; // will not affect child process
    callback('from parent ' . $var);
    // make sure the parent outlives the child process
    pcntl_wait($status);
}

echo "Process continue... \n";