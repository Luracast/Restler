<?php
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    $require_comments=$file[0]=='.';
    $file = '../'.$file;
    $filepath = realpath($file);
    $basepath = realpath('../../');
    if(strpos($basepath,$filepath)===0){
        #trying to get the source outside restler examples
        die('not allowed');
    }
    if(!file_exists($file))die('file not found');
    $text = file_get_contents($file);
    $file = pathinfo($file, PATHINFO_FILENAME).'.php';
    if(!$require_comments)$text = strip_comments($text);
    die($file.'<pre id="php">'.htmlspecialchars($text)."</pre>");
}else {
    die('no file specified');
}
function strip_comments($fileStr) {
    $newStr='';
    $commentTokens = array(T_COMMENT);

    //if (defined('T_DOC_COMMENT'))
    //$commentTokens[] = T_DOC_COMMENT; // PHP 5

    $tokens = token_get_all($fileStr);

    foreach ($tokens as $token) {
        if (is_array($token)) {
            if (in_array($token[0], $commentTokens))
            continue;

            $token = $token[1];
        }

        $newStr .= $token;
    }

    return $newStr;
}

