<?php

// http://stackoverflow.com/questions/7775894/implementing-a-memory-efficient-arrayaccess-class-in-php

class TreeNode implements ArrayAccess {
    public static $instances = 0;
    protected $nodes = array();
    
    public function __construct($nodes=array())
    {
        self::$instances++;
        foreach ($nodes as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }
    
    // inverted params as requested by question
    public function set($value, $offset)
    {
        $this->offsetSet($offset, $value);
    }
    
    public function get($offset)
    {
        $this->offsetGet($offset, $value);
    }
    
    /*
     * ArrayAccess implementation
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->nodes[] = is_array($value) ? new TreeNode($value) : $value;
        } else {
            $this->nodes[$offset] = is_array($value) ? new TreeNode($value) : $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->nodes[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->nodes[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->nodes[$offset]) ? $this->nodes[$offset] : null;
    }
    
    /*
     * Magic Methods wrapping ArrayAccess
     */
    public function __get($name) {
        return $this->offsetGet($name);
    }
    
    public function __set($name, $value) {
        return $this->offsetSet($name, $value);
    }
}

$__memory = memory_get_usage();

$nodes = array(
    'head' => array(
        'doctype' => '<!DOCTYPE html>',
        'html' => "<html>",
        'head' => '<head>',
        'title' => '<title>',
        'base' => '<base>',
        'link' => '<link>',
        'meta' => '<meta>',
        'style' => '<style>',
        'script' => '<script>',
        'noscript' => '<noscript>',
    ),
    'sections' => array(
        'body' => '<body>',
        'article' => '<article>',
        'nav' => '<nav>',
        'aside' => '<aside>',
        'section' => '<section>',
        'header' => '<header>',
        'footer' => '<footer>',
        'h1-h6' => array(
            'h1' => '<h1>',
            'h2' => '<h2>',
            'h3' => '<h3>',
            'h4' => '<h4>',
            'h5' => '<h5>',
            'h6' => '<h6>',
        ),
        'hgroup' => '<hgroup>',
        'address' => '<address>',
    ),
    'grouping' => array(
        'p' => '<p>',
        'hr' => '<hr>',
        'pre' => '<pre>',
        'blockquote' => '<blockquote>',
        'ol' => '<ol>',
        'ul' => '<ul>',
        'li' => '<li>',
        'dl, dt, dd' => array(
            'dl' => '<dl>',
            'dt' => '<dt>',
            'dd' => '<dd>',
        ),
        'figure' => '<figure>',
        'figcaption' => '<figcaption>',
        'div' => '<div>',
    ),
    'tables' => array(
       'table' => '<table>',
        'caption' => '<caption>',
        'thead' => '<thead>',
        'tbody' => '<tbody>',
        'tfoot' => '<tfoot>',
        'tr' => '<tr>',
        'th' => '<th>',
        'td' => '<td>',
        'col' => '<col>',
        'colgroup' => '<colgroup>',
    ),
    'forms' => array(
        'form' => '<form>',
        'fieldset' => '<fieldset>',
        'legend' => '<legend>',
        'label' => '<label>',
        'input' => '<input>',
        'button' => '<button>',
        'select' => '<select>',
        'datalist' => '<datalist>',
        'optgroup' => '<optgroup>',
        'option' => '<option>',
        'textarea' => '<textarea>',
        // forms 2
        'keygen' => '<keygen>',
        'output' => '<output>',
        'progress' => '<progress>',
        'meter' => '<meter>',
    ),
    'interactive' => array(
       'details' => '<details>',
        'summary' => '<summary>',
        'command' => '<command>',
        'menu' => '<menu>',
    ),
    'edit' => array(
        'del, ins' => array(
            'del' => '<del>',
            'ins' => '<ins>',
        ),
    ),
    'embedded' => array(
        'img' => '<img>',
        'iframe' => '<iframe>',
        'embed' => '<embed>',
        'object' => '<object>',
        'param' => '<param>',
        'video' => '<video>',
        'audio' => '<audio>',
        'source' => '<source>',
        'canvas' => '<canvas>',
        'track' => '<track>',
        'map' => '<map>',
        'area' => '<area>',
    ),
    'text' => array(
       'a' => '<a>',
        'em' => '<em>',
        'strong' => '<strong>',
        'i, b' => array(
            'i' => '<i>',
            'b' => '<b>',
        ),
        'u' => '<u>',
        's' => '<s>',
        'small' => '<small>',
        'abbr' => '<abbr>',
        'q' => '<q>',
        'cite' => '<cite>',
        'dfn' => '<dfn>',
        'sub, sup' => array(
            'sub' => '<sub>',
            'sup' => '<sup>',
        ),
        // text 2
        'time' => '<time>',
        'code' => '<code>',
        'kbd' => '<kbd>',
        'samp' => '<samp>',
        'var' => '<var>',
        'mark' => '<mark>',
        'bdi' => '<bdi>',
        'bdo' => '<bdo>',
        'ruby, rt, rp' => array(
            'ruby' => '<ruby>',
            'rt' => '<rt>',
            'rp' => '<rp>',
        ),
        'span' => '<span>',
        'br' => '<br>',
        'wbr' => '<wbr>',
    ),
);

$_memory = memory_get_usage();
printf("source used %0.4f Bytes\n", $_memory - $__memory);

$_time = microtime(true);
$root = new TreeNode($nodes);
$time = microtime(true);

$memory = memory_get_usage();
printf("TreeNode used %0.4f Bytes for %d nodes\n", $memory - $_memory, TreeNode::$instances);
printf(" it took %0.6f seconds to create them\n", $time - $_time);

unset($nodes);
$memory = memory_get_usage();
printf("TreeNode used %0.4f Bytes for %d nodes (with source unset)\n", $memory - $_memory, TreeNode::$instances);

printf("thats %0.4f Bytes per node", ($memory - $_memory) / TreeNode::$instances);

var_dump($root['grouping']['dl, dt, dd']['dd']);