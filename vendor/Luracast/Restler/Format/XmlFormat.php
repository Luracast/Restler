<?php
namespace Luracast\Restler\Format;

/**
 * XML Markup Format for Restler Framework
 * @category   Framework
 * @package    Restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc3
 */
use Luracast\Restler\Data\Util;
use Luracast\Restler\Exception;
use Luracast\Restler\RestException;
use SimpleXMLElement;

class XmlFormat extends Format
{
    public static $importRootNameAndAttributesFromXml = false;
    public static $parseAttributes = true;
    public static $parseNamespaces = false;
    public static $attributeNames = array('xmlns');
    public static $nameSpaces = array();
    /**
     * Default name for the root node.
     *
     * @var string $rootNodeName
     */
    public static $rootName = 'response';
    public static $defaultTagName = 'item';
    const MIME = 'application/xml';
    const EXTENSION = 'xml';

      public function encode($data, $humanReadable = false)
    {
        return $this->toXML(
                Util::objectToArray($data, false),
                self::$rootName, $humanReadable
        );
    }

    public function decode($data)
    {
        try {
            if ($data == '') {
                return array();
            }
            return $this->toArray($data);
        } catch (\RuntimeException $e) {
            throw new RestException(400,
                    "Error decoding request. " . $e->getMessage());
        }
    }

    /**
     * determine if a variable is an associative array
     */
    public function isAssoc($array)
    {
        return is_array($array) && 0 !== count(
            array_diff_key($array, array_keys(array_keys($array)))
        );
    }

    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recursively loops through
     * and builds up an XML document.
     *
     * @param array  $data
     * @param string $rootNodeName
     *            - what you want the root node to be
     *            - defaults to data.
     * @param SimpleXMLElement $xml
     *            - should only be used recursively
     * @return string XML
     * @link http://bit.ly/n85yLi
     */
    public function toXML($data,
                          $rootNodeName = 'result',
                          $humanReadable = false,
                          &$xml = null)
    {
        // turn off compatibility mode as simple xml
        // throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }
        if (is_null($xml)) {
            if (empty(self::$nameSpaces)) {
                $xml = @simplexml_load_string("<$rootNodeName/>");
            } else {
                $str = "<$rootNodeName";
                foreach (self::$nameSpaces as $kn => $vn) {
                    $str .= ' ' . $kn . '="' . $vn . '"';
                }
                $str .= '/>';
                $xml = @simplexml_load_string($str);
            }
        }
        if (is_array($data)) {
            $numeric = 0;
            // loop through the data passed in.
            foreach ($data as $key => $value) {
                // no numeric keys in our xml please!
                if (is_numeric($key)) {
                    $numeric = 1;
                    if (self::$rootName == $rootNodeName) {
                        $key = self::$defaultTagName;
                    } else {
                        $key = $rootNodeName;
                    }
                }
                // delete any char not allowed in XML element names
                $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);
                // if there is another array found recursively
                // call this function
                if (is_array($value)) {
                    $node = $this->isAssoc($value) || $numeric
                            ? $xml->addChild($key)
                            : $xml;
                    // reclusive call.
                    if ($numeric) {
                        $key = 'anon';
                    }
                    $this->toXML($value, $key, $humanReadable, $node);
                } else {
                    // add single node or attribute
                    $value = htmlspecialchars($value);
                    if (in_array($key, self::$attributeNames)) {
                        $xml->addAttribute($key, $value);
                    } else {
                        $xml->addChild($key, $value);
                    }
                }
            }
        } else {
            // if given data is a string or number
            // simply wrap it as text node to root
            if (is_bool($data)) {
                $data = $data ? 'true' : 'false';
            }
            $xml = @simplexml_load_string("<$rootNodeName>" .
                        htmlspecialchars($data) . "</$rootNodeName>");
            // $xml->{0} = $data;
        }
        if (!$humanReadable) {
            if (is_object($xml))
                return $xml->asXML();
            return $xml;
        } else {
            $dom = dom_import_simplexml($xml)->ownerDocument;
            $dom->formatOutput = true;

            return $dom->saveXML();
        }
    }

    /**
     * Convert an XML document to a multi dimensional array
     * Pass in an XML document (or SimpleXMLElement object) and this
     * recursively loops through and builds a representative array
     *
     * @param string $xml
     *            - XML document
     *            - can optionally be a SimpleXMLElement object
     * @return array ARRAY
     * @link http://bit.ly/n85yLi
     */
    public function toArray($xml, $ns = null, $firstCall = true)
    {
        try {
            $xml = new SimpleXMLElement($xml);
        } catch (\Exception $e) {
            return (string) $xml;
        }
        $hasChildren = false;
        if ($xml->children()) {
            $hasChildren = true;
        } elseif (is_array($ns)) {
            foreach ($ns as $namespace => $uri) {
                if ($xml->children($uri)->count()) {
                    $hasChildren = true;
                    break;
                }
            }
        }
        if (!$hasChildren) {
            $r = (string) $xml;
            if ($r == 'true' || $r == 'false') {
                $r = $r == 'true';
            }

            return $r;
        }
        $arr = array();
        if ($firstCall) {
            if(self::$importRootNameAndAttributesFromXml){
                // reset the attribute names list
                self::$attributeNames = array();
                self::$rootName = $xml->getName();
            }
            if (self::$parseNamespaces) {
                foreach ($xml->getDocNamespaces(true) as $namespace => $uri) {
                    if ($namespace == '') {
                        self::$nameSpaces [''] = (string) $uri;
                        // $arr['xmlns'] = (string) $uri;
                    } else {
                        self::$nameSpaces [$namespace] = (string) $uri;
                        // $arr['xmlns:' . $namespace] = (string) $uri;
                    }
                }
                $ns = self::$nameSpaces;
            }
        }
        if (self::$parseAttributes) {
            foreach ($ns as $namespace => $uri) {
                if ($namespace == '') {
                    continue;
                }
                foreach ($xml->attributes($uri) as $attName => $attValue) {
                    //echo "ATTRIB " . $attName . " of NAME " . $xml->getName() . PHP_EOL;
                    $attName = "_{$namespace}_{$attName}";
                    $arr [$attName] = (string) $attValue;
                    // add to attribute list for round trip support
                    self::$attributeNames [] = $attName;
                }
            }
            foreach ($xml->attributes() as $attName => $attValue) {
                //echo "ATTRIB " . $attName . " of NAME " . $xml . PHP_EOL;
                $arr [$attName] = (string) $attValue;
                // add to attribute list for round trip support
                self::$attributeNames [] = $attName;
            }
        }
        $children = $xml->children();
        foreach ($children as $key => $node) {
            //echo "NAME " . $key . PHP_EOL;
            $node = $this->toArray($node, $ns, false);
            if (is_string($node)) {
                // echo "NAME ".$key.PHP_EOL;
                // echo $node;
                // print_r($arr[$key]);
                // echo PHP_EOL;
            }
            // support for 'anon' non-associative arrays
            if ($key == 'anon') {
                $key = count($arr);
            }
            // if the node is already set, put it into an array
            if (isset($arr [$key])) {
                if (!is_array($arr [$key]) || @$arr [$key] [0] == null) {
                    $arr [$key] = array(
                            $arr [$key]
                    );
                }
                $arr [$key] [] = $node;
            } else {
                $arr [$key] = $node;
            }
        }
        if (is_array($ns)) {
            foreach ($ns as $namespace => $uri) {
                if ($namespace == '') {
                    continue;
                }
                $children = $xml->children($uri);
                foreach ($children as $key => $node) {
                    //echo "NAME " . $key . PHP_EOL;
                    $node = $this->toArray($node, $ns, false);
                    // support for 'anon' non-associative arrays
                    if ($key == 'anon') {
                        $key = count($arr);
                    }
                    $key = "_{$namespace}_{$key}";
                    // if the node is already set, put it into an array
                    if (isset($arr [$key])) {
                        if (!is_array($arr [$key])
                                || @$arr [$key] [0] == null) {
                            $arr [$key] = array(
                                    $arr [$key]
                            );
                        }
                        $arr [$key] [] = $node;
                    } else {
                        $arr [$key] = $node;
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * When you decode an XML its structure is copied to the static vars
     * we can use this function to echo them out and then copy paste inside
     * our service methods
     *
     * @return string PHP source code to reproduce the configuration
     */
    public static function exportCurrentSettings()
    {
        $s = 'XmlFormat::$rootName = "' . (self::$rootName) . "\";\n";
        $s .= 'XmlFormat::$attributeNames = ' .
            (var_export(self::$attributeNames, true)) . ";\n";
        $s .= 'XmlFormat::$defaultTagName = "' .
            self::$defaultTagName . "\";\n";
        $s .= 'XmlFormat::$parseAttributes = ' .
            (self::$parseAttributes ? 'true' : 'false') . ";\n";
        $s .= 'XmlFormat::$parseNamespaces = ' .
            (self::$parseNamespaces ? 'true' : 'false') . ";\n\n\n";
        if (self::$parseNamespaces) {
            $s .= 'XmlFormat::$nameSpaces = ' .
            (var_export(self::$nameSpaces, true)) . ";\n";
        }

        return $s;
    }

}

