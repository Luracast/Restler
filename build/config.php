<?php
define('PROJECT_ROOT', realpath('../'));

define('RESTLER_DIR', 'vendor/Luracast/Restler');
define('MINIFIED_DIR', 'restler_minified');
define('DOCS_DIR', 'docs');
define('EXAMPLES_DIR', 'examples');
define('BUILD_DIR', 'build');
define('APIGEN_DIR', 'apigen');
define('CODE_SNIFFER_DIR', 'codesniffer');
define('PHPDOC_DIR', 'phpDocumentor2');

define('RESTLER_PATH', PROJECT_ROOT.DIRECTORY_SEPARATOR.RESTLER_DIR);
define('MINIFIED_PATH', PROJECT_ROOT.DIRECTORY_SEPARATOR.MINIFIED_DIR);
define('DOCS_PATH', PROJECT_ROOT.DIRECTORY_SEPARATOR.DOCS_DIR);
define('EXAMPLES_PATH', PROJECT_ROOT.DIRECTORY_SEPARATOR.EXAMPLES_DIR);
#build paths
define('BUILD_PATH', PROJECT_ROOT.DIRECTORY_SEPARATOR.BUILD_DIR);
define('APIGEN_PATH', BUILD_PATH.DIRECTORY_SEPARATOR.APIGEN_DIR);
define('CODE_SNIFFER_PATH', BUILD_PATH.DIRECTORY_SEPARATOR.CODE_SNIFFER_DIR);
define('PHPDOC_PATH', BUILD_PATH.DIRECTORY_SEPARATOR.PHPDOC_DIR);

define('API_DOC_TITLE', 'Luracast Restler v3.0 API Documentation');

set_include_path(get_include_path() . PATH_SEPARATOR . RESTLER_PATH);