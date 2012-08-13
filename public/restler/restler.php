<?php
/** Compatibility bootstrap for R2 examples */
/** For the R3 bootstrap see the root project folder vendor/restler.php */

/** locate the caller file and add it's dirname to the include path  */
/** This way we ensure that the API classes are accessible */
$debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
foreach ($debugBacktrace as $callee)
    if (isset($callee['file'])) {
        set_include_path(dirname($callee['file'])
            . PATH_SEPARATOR . get_include_path());
        break;
    }

require_once __DIR__.'/../../vendor/restler.php';

/** Just to be certain that new Restler() is understood as expected by R3
 * without use statements */
if (!class_exists('Restler'))
    class_alias('Luracast\Restler\Restler', 'Restler');
