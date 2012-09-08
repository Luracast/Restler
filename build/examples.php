<?php
include_once 'config.php';
include_once 'markdown/markdown_extended.php';
include_once 'templates/templateengine.php';
include_once PROJECT_ROOT.'/vendor/restler.php';
//include_once PROJECT_ROOT.'/vendor/Luracast/Restler/Restler.php';

header('Content-Type: text/plain');
error_reporting(E_ALL & ~E_NOTICE);
echo 'building Examples...
';

use \Luracast\Restler\Restler;

$styles = array(
        'resources/bootstrap.min.css',
        'resources/style.css',
        'resources/jquery.snippet.min.css',
        'resources/facebox.css',
        'resources/hacks.css'
);
$scripts = array(
        'resources/jquery-1.7.2.min.js',
        'resources/jquery.snippet.min.js',
        'resources/facebox.js',
        'resources/bootstrap.min.js'
);

$version = intval(Restler::VERSION);

TemplateEngine::render('css/style.css.php', array(), BUILD_PATH.'/resources');

#copy(BUILD_PATH.'/resources', EXAMPLES_PATH);
echo shell_exec("cp -r ".BUILD_PATH.'/resources'. ' '.EXAMPLES_PATH);

file_put_contents(PROJECT_ROOT.DIRECTORY_SEPARATOR.'README.html', '
<!DOCTYPE html>
<html>
<head>
<title>Luracast Restler '.$version.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="examples/resources/style.css"/>
</head>
<body>
<article>
'.MarkdownExtended(file_get_contents(PROJECT_ROOT.DIRECTORY_SEPARATOR.'README.md'), array('pre'=>'prettyprint')).'
</article>
</body>
</html>');

$files = scandir(EXAMPLES_PATH);
$all=array();
$tags=array();
foreach($files as $filename){
    $fullpath = EXAMPLES_PATH.DIRECTORY_SEPARATOR.$filename;
    if($filename[0]=='_' && is_dir($fullpath)) {
        $o = new stdClass();
        $o->fullpath=$fullpath;
        $o->folder = $filename;
        echo PHP_EOL.$fullpath;
        #echo exec("chmod -R 0777 '$fullpath/'");
        //chmod($fullpath, 0777);
        chdir($fullpath);
        set_include_path($fullpath . PATH_SEPARATOR . get_include_path());
        $metadata=get_metadata($fullpath.DIRECTORY_SEPARATOR.'index.php');
        /*
        echo PHP_EOL;
        print_r($metadata);
        echo PHP_EOL;
        */
        $content = file_get_contents($fullpath.DIRECTORY_SEPARATOR.'index.php');
        $content = str_replace(array('<?php','$r->handle();'),'', $content);
        //echo $content;
        //continue;
        eval($content);
        $isRestler2 = intval(Restler::VERSION)==2;
        //include_once $fullpath.DIRECTORY_SEPARATOR.'index.php';
        #$r = new Restler();
        $routes = get_protected_property('Restler', $isRestler2 ? 'routes' : '_routes', $r);
        $name = explode('_', $filename);
        array_shift($name);
        array_shift($name);
        $o->version = $version;
        $o->title = !empty($metadata['title'])? $metadata['title'] : ucwords(implode(' ', $name)).' Example';
        $o->tagline = $metadata['tagline'];
        $o->description = $metadata['description'];
        $o->summary = null;
        $o->tags = $metadata['tags'];
        $o->usage = $metadata['usage'];
        $o->content = $metadata['content'];
        $o->requires = $metadata['requires'];
        $helpers = $metadata['helpers'];
        $helpers = empty($helpers) ? array() : explode(',', str_replace(' ', '', $helpers));
        $o->routes=array();
        $api_classes=array();
        foreach ($routes as $httpMethod => $route) {
            $gets = array_keys($route);
            $max = 10;
            foreach ($gets as $get) {
                $info = $route[$get];
                $api_classes[$info[$isRestler2 ? 'class_name' : 'className']]=TRUE;
                if($isRestler2){
                    $info['className']=$info['class_name'];
                    $info['methodName']=$info['method_name'];
                    unset($info['class_name']);
                    unset($info['method_name']);
                }
                if(empty($get))$get="./";
                $info['httpMethod']=$httpMethod;
                $info['url']=$get;
                $o->routes[]=$info;
                $max = max(strlen($httpMethod.$get)+1, $max);
            }
            $o->routes_max = $max;
        }
        $examples = $metadata['e'];
        $o->examples=array();
        if(!empty($examples)){
            foreach ($examples as $example) {
                $info = array();
                $info['httpMethod']=$example[0];
                if(!$example[0]=='GET')continue;
                if(empty($example[1]))$example[1]="./";
                if(isset($example[3])&& $example[2]=='returns'){
                    $info['result'] = $example[3];
                }
                $o->examples[$example[1]]=$info;
            }
            #var_export($routes);
        }
        $api_classes=",".implode(',',array_keys($api_classes)).",";
        $authClasses = ",".implode(',', get_protected_property('Restler', $isRestler2 ? 'auth_classes' : '_authClasses', $r)).",";
        $api_files = array();
        $classes = array();
        foreach(get_declared_classes() as $class){
            $parts = explode('\\',$class);
            $className = end($parts);
            $classes[strtolower($className)]=array($className, $class);
        }
        foreach (get_included_files() as $api_file) {
            if(strpos($api_file,$filename)>0){
                $api = pathinfo($api_file, PATHINFO_FILENAME);
                $type = 'helper';
                if(stripos($authClasses, ",$api,")!==FALSE){
                    $type='auth';
                }elseif (stripos($api_classes, ",$api,")!==FALSE){
                    $type='api';
                }
                if(isset($classes[$api])){
                    $api = $classes[$api][0];
                }
                $api.='.'.pathinfo($api_file, PATHINFO_EXTENSION);
                $api_files[$api] = array('type'=>$type,
                    'path'=>\Luracast\Restler\Util::removeCommonPath($api_file, EXAMPLES_PATH));
            }
        }
        foreach ($helpers as $helper){
            $parts = explode('\\',$helper);
            $helperName = end($parts);
            $helper = implode('/', $parts);
            $api = $helperName.'.php';
            $api_files[$api] = array('type'=>'helper',
                'path'=>$filename.DIRECTORY_SEPARATOR.$helper.'.php');

        }
        $o->local_files=array('index.php'=>array('type'=>'gateway','path'=>$filename.DIRECTORY_SEPARATOR.'index.php'));
        $o->local_files+=$api_files;
        $formatMap = get_protected_property('Restler', $isRestler2 ? 'format_map': '_formatMap', $r);
        unset($formatMap['extensions']);
        $formats = array_unique(array_values($formatMap));
        $format_files = array('restler.php'=>array('type'=>'framework', 'path'=>'../restler/restler.php'));
        foreach ($formats as $format) {
            $format_file = strtolower($format);
            $format_path = RESTLER_PATH.DIRECTORY_SEPARATOR.$format_file;
            if(is_dir($format_path)){
                $format_files["$format_file folder"]=array('type'=>'format',
                'path'=>"../restler/$format_file/$format_file.php");
            }elseif (is_file($format_path.'.php')){
                $format_files["$format_file.php"]=array('type'=>'format',
                'path'=>"../restler/$format_file.php");
            }
        }
        $o->restler_files = $format_files;

        #file_put_contents($fullpath.DIRECTORY_SEPARATOR.'readme.md', $markdown);
        #file_put_contents($fullpath.DIRECTORY_SEPARATOR.'readme.htm', MarkdownExtended($markdown));
        $o->styles=$styles;
        $o->scripts=$scripts;
        foreach ($o->tags as $tag) {
            $tagInfo = new stdClass();
            $tagInfo->folder = $o->folder;
            $tagInfo->title = $o->title;
            $tagInfo->tagline = $o->tagline;
            $tagInfo->tags = $o->tags;
            isset($tags[$tag]) ? $tags[$tag][]=$tagInfo : $tags[$tag]=array($tagInfo);
        }
        $all[]=$o;
    }
}
//print_r($tags);
//sort tags
ksort($tags);
$tagStr='';
foreach ($tags as $key => $value) {
    $titles = array();
    $links = array();
    foreach ($value as $v) {
        $titles[]=$v->title;
        $links[]="<a href=\"../".$v->folder."/readme.html\">{$v->title}</a>";
    }
    $titles = implode(', ', $titles);
    $links = implode(' ', $links);
    $links = htmlentities($links);
    $tagStr.=PHP_EOL."<li><tag title=\"".ucwords($key)." Example(s)\" data-content=\"$links\">$key</tag>";
    if(count($value)>1)
        $tagStr.="<badge>".count($value)."</badge>";
    $tagStr.="</li>".PHP_EOL;
}
$links=array();
foreach ($all as $o) {
    $link=array();
    $link['href']=$o->folder.'/readme.html';
    $link['name']=str_replace(' Example', '', $o->title);
    $link['tagline']=$o->tagline;
    $link['description']=truncate($o->description, 100);
    $links[]=$link;
}
$main = new stdClass();
$main->href = 'index.html';
$main->name ='Examples';
$main->tags = $tags;
$main->tagStr = $tagStr;
#array_unshift($links, $link);
#print_r($links);
#print_r($all);
#die("/(<dd></dd>)\s+</dl>\s+(<pre><code>[^<]+</code></pre>)/");
$counter=0;
foreach ($all as $o) {
    $o->links = $links;
    $o->main = $main;
    $o->link_prefix ='../';
    $o->count=$counter+1;
    pad_foreach($o->routes, array('httpMethod','url'));
    #print_r($o->routes);
    TemplateEngine::render(($counter ? 'readme2.md.php' : 'readme.md.php'), (array)$o, $o->fullpath, 'readme.md');
    #foreach ($o->examples as $key => &$value) {
    #$value['result']=htmlentities($value['result']);
    #}
    #print_r($o);
    $htm = TemplateEngine::render(($counter ? 'readme2.html.php' : 'readme.html.php'), (array)$o);
    $htm = str_replace('<?', '&lt;?', $htm);
    $htm = str_replace('?'.'>', '?&gt;', $htm);
    $htm = preg_replace("/(<dd><\/dd>)\s+<\/dl>\s+(<pre><code>[^<]+<\/code><\/pre>)/", '<dd>$2</dd>', $htm);
    file_put_contents($o->fullpath.DIRECTORY_SEPARATOR.'readme.html', $htm);
    $counter++;
}
//change the relative path to match index page
$main->tagStr = str_replace('../', '', $tagStr);
$indexPage=new stdClass();
$indexPage->version=$version;
$indexPage->links=$links;
$indexPage->main=$main;
$indexPage->styles=$styles;
$indexPage->scripts=$scripts;
TemplateEngine::render('index.html.php',(array)$indexPage , EXAMPLES_PATH);

echo PHP_EOL.'completed';

#print_r($all);
function get_protected_property($className, $property, $instance) {
    $relfection_property = new ReflectionProperty($className, $property);
    $relfection_property->setAccessible(TRUE);
    return $relfection_property->getValue($instance);
}

function get_metadata($file){

    $default_headers = array(
        'title'=>'Title',
        'tagline'=>'Tagline',
        'tags'=>'Tags',
        'description'=>'Description',
        'e1'=>'Example 1',
        'e2'=>'Example 2',
        'e3'=>'Example 3',
        'e4'=>'Example 4',
        'e5'=>'Example 5',
        'e6'=>'Example 6',
        'e7'=>'Example 7',
        'e8'=>'Example 8',
        'e9'=>'Example 9',
        'e10'=>'Example 10',
        'usage'=>'Usage',
        'helpers'=>'Helpers',
        'content'=>'Content',
        'requires'=>'Requires',
    );
    $m = get_file_data($file, $default_headers);
    /*
    echo PHP_EOL;
    print_r($m);
    echo PHP_EOL;
    */
    $r = array('e'=>array());
    foreach ($m as $key => $value) {
        if($key[0]=='e'){
            if(!empty($value)){
                $parts = explode('returns',$value);
                $valarr = explode(' ', $parts[0], 3);
                $valarr[2] ='returns';
                $valarr []= $parts[1];
                foreach ($valarr as $vkey => $vvalue) {
                    $valarr[$vkey] = str_replace('*//*', '*/*',trim($vvalue));
                }
                $r['e'][intval(substr($key, 1))]= $valarr;
            }
        }else if($key=='tags'){
            $r[$key] = explode(',', $value);
            foreach ($r[$key] as $k => $v) {
                $r[$key][$k]=trim($v);
            }
        }else{
            $r[$key] = str_replace('*//*', '*/*',trim($value));
        }
    }
    #print_r($r);
    return $r;
}

function truncate ($string, $limit=10, $break=".", $trailing='...')
{
    // return with no change if string is shorter than $limit
    if(strlen($string) <= $limit) return $string;

    // is $break present between $limit and the end of the string?
    if(false !== ($breakpoint = strpos($string, $break, $limit))) {
        if($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $trailing;
        }
    }

    return $string;
}

function pad_foreach(&$arr, $varnames, $prefix='spaced_'){
    $lengths=array();
    foreach ($arr as $key => $value) {
        if(is_array($value))$value=(object)$value;
        foreach ($varnames as $varname) {
            if(!isset($lengths[$varname]))$lengths[$varname]=0;
            $lengths[$varname]=max(strlen($value->$varname)+1, $lengths[$varname]);
        }
    }
    foreach ($arr as $key => $value) {
        if(is_array($value))$value=(object)$value;
        foreach ($varnames as $varname) {
            $arr[$key][$prefix.$varname]=$value->$varname.str_repeat(' ', $lengths[$varname]-strlen($value->$varname));
        }
    }
}

function get_file_data( $file, $default_headers) {
    // We don't need to write to the file, so just open for reading.
    $fp = fopen( $file, 'r' );

    // Pull only the first 8kiB of the file in.
    $file_data = fread( $fp, 8192 );

    // PHP will close file handle, but we are good citizens.
    fclose( $fp );

    $file_data = explode('*/',$file_data);
    array_pop($file_data);
    $file_data = implode('',$file_data);

    $all_headers = $default_headers;

    $r = array();
    foreach ($all_headers as $field => $name ) {
        $r[$field]='';
    }

    $lastKey='';
    while (preg_match('/^ ?(\w+ ?\d{0,1}): ?/m',$file_data,$matches)){
        list($subject, $key) = $matches;
        $parts = explode($subject,$file_data,2);
        $file_data = end($parts);
        if(!empty($lastKey) && !empty($parts[0])){
            //extract body
            $name = array_search($lastKey,$all_headers);
            if($name!==false){
                $r[$name]= trim($parts[0]);
            }
        }
        $lastKey=$key;
    }
    if(!empty($lastKey) && !empty($file_data)){
        //extract body for the final one
        $name = array_search($lastKey,$all_headers);
        if($name!==false){
            $r[$name]= trim($file_data);
        }
    }
    return $r;
}

