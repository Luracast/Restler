<?php
namespace Luracast\Restler\Format;

use Luracast\Restler\Data\Object;
use Luracast\Restler\RestException;
use SimpleXMLElement;
use XMLWriter;

/**
 * XML Markup Format for Restler Framework
 *
 * @category   Framework
 * @package    Restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 */
class XmlFormat extends Format
{
    const MIME = 'application/xml';
    const EXTENSION = 'xml';

    // ==================================================================
    //
    // Properties related to reading/parsing/decoding xml
    //
    // ------------------------------------------------------------------
    public static $importSettingsFromXml = false;
    public static $parseAttributes = true;
    public static $parseNamespaces = true;
    public static $parseTextNodeAsProperty = true;

    // ==================================================================
    //
    // Properties related to writing/encoding xml
    //
    // ------------------------------------------------------------------
    public static $useTextNodeProperty = true;
    public static $useNamespaces = true;
    public static $cdataNames = array();

    // ==================================================================
    //
    // Common Properties
    //
    // ------------------------------------------------------------------
    public static $attributeNames = array();
    public static $textNodeName = 'text';
    public static $namepaces = array();
    public static $namespacedProperties = array();
    /**
     * Default name for the root node.
     *
     * @var string $rootNodeName
     */
    public static $rootName = 'response';
    public static $defaultTagName = 'item';

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
            (self::$parseNamespaces ? 'true' : 'false') . ";\n";
        if (self::$parseNamespaces) {
            $s .= 'XmlFormat::$nameSpaces = ' .
                (var_export(self::$namepaces, true)) . ";\n";
            $s .= 'XmlFormat::$nameSpacedProperties = ' .
                (var_export(self::$namespacedProperties, true)) . ";\n";
        }

        return $s;
    }

    public function encode($data, $humanReadable = false)
    {
        $data = Object::toArray($data);
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', $this->charset);
        if ($humanReadable) {
            $xml->setIndent(true);
            $xml->setIndentString('    ');
        }
        static::$useNamespaces && isset(static::$namespacedProperties[static::$rootName])
            ? $xml->startElementNs(
            static::$namespacedProperties[static::$rootName],
            static::$rootName,
            static::$namepaces[static::$namespacedProperties[static::$rootName]]
        )
            : $xml->startElement(static::$rootName);
        if (static::$useNamespaces) {
            foreach (static::$namepaces as $prefix => $ns) {
                if (isset(static::$namespacedProperties[static::$rootName])
                    && static::$namespacedProperties[static::$rootName] == $prefix
                )
                    continue;
                $xml->writeAttribute('xmlns:' . $prefix, $ns);
            }
        }
        $this->write($xml, $data, static::$rootName);
        $xml->endElement();
        return $xml->outputMemory();
    }

    public function write(XMLWriter $xml, $data, $parent)
    {
        $text = '';
        if (is_array($data)) {
            if (static::$useTextNodeProperty && isset($data[static::$textNodeName])) {
                $text = $data[static::$textNodeName];
                unset($data[static::$textNodeName]);
            }
            foreach ($data as $key => $value) {
                if (is_numeric($key)) {
                    if (is_string($value)) {
                        $text .= $value;
                        continue;
                    }
                    $key = static::$defaultTagName;
                }
                $useNS = static::$useNamespaces
                    && isset(static::$namespacedProperties[$key])
                    && false === strpos($key, ':');
                if (is_array($value)) {
                    if ($value == array_values($value)) {
                        //numeric array, create siblings
                        foreach ($value as $v) {
                            $useNS
                                ? $xml->startElementNs(
                                static::$namespacedProperties[$key],
                                $key,
                                null
                            )
                                : $xml->startElement($key);
                            $this->write($xml, $v, $key);
                            $xml->endElement();
                        }
                    } else {
                        $useNS
                            ? $xml->startElementNs(
                            static::$namespacedProperties[$key],
                            $key,
                            null
                        )
                            : $xml->startElement($key);
                        $this->write($xml, $value, $key);
                        $xml->endElement();
                    }
                    continue;
                } elseif (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                if (in_array($key, static::$attributeNames)) {
                    $useNS
                        ? $xml->writeAttributeNs(
                        static::$namespacedProperties[$key],
                        $key,
                        null,
                        $value
                    )
                        : $xml->writeAttribute($key, $value);
                } else {
                    $useNS
                        ? $xml->startElementNs(
                        static::$namespacedProperties[$key],
                        $key,
                        null
                    )
                        : $xml->startElement($key);
                    $this->write($xml, $value, $key);
                    $xml->endElement();
                }
            }
        } else {
            $text = (string)$data;
        }
        if (!empty($text) or $text == 0) {
            in_array($parent, static::$cdataNames)
                ? $xml->writeCdata($text)
                : $xml->text($text);
        }
    }

    public function decode($data)
    {
        try {
            if ($data == '') {
                return array();
            }
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($data,
                "SimpleXMLElement", LIBXML_NOBLANKS | LIBXML_NOCDATA);
            if (false === $xml) {
                $error = end(libxml_get_errors());
                throw new RestException(400, 'Malformed XML. '
                    . trim($error->message, "\r\n") . ' at line ' . $error->line);
            }
            libxml_clear_errors();
            if (static::$importSettingsFromXml) {
                static::$attributeNames = array();
                static::$namespacedProperties = array();
                static::$namepaces = array();
                static::$rootName = $xml->getName();
                $namespaces = $xml->getNamespaces();
                if (count($namespaces)) {
                    static::$namespacedProperties[static::$rootName] = end(array_keys($namespaces));
                }
            }
            $data = $this->read($xml);
            return $data;
        } catch (\RuntimeException $e) {
            throw new RestException(400,
                "Error decoding request. " . $e->getMessage());
        }
    }

    public function read(SimpleXMLElement $xml, $namespaces = null)
    {
        $r = array();
        $text = (string)$xml;

        if (static::$parseAttributes) {
            $attributes = $xml->attributes();
            foreach ($attributes as $key => $value) {
                if (static::$importSettingsFromXml
                    && !in_array($key, static::$attributeNames)
                ) {
                    static::$attributeNames[] = $key;
                }
                $r[$key] = static::setType((string)$value);
            }
        }
        $children = $xml->children();
        foreach ($children as $key => $value) {
            if (isset($r[$key])) {
                if (is_array($r[$key]) && $r[$key] != array_values($r[$key])) {
                    $r[$key] = array($r[$key]);
                } else {
                    $r[$key] = array($r[$key]);
                }
                $r[$key][] = $this->read($value);
            } else {
                $r[$key] = $this->read($value);
            }
        }

        if (static::$parseNamespaces) {
            if (is_null($namespaces))
                $namespaces = $xml->getDocNamespaces(true);
            foreach ($namespaces as $prefix => $ns) {
                static::$namepaces[$prefix] = $ns;
                if (static::$parseAttributes) {
                    $attributes = $xml->attributes($ns);
                    foreach ($attributes as $key => $value) {
                        if (isset($r[$key])) {
                            $key = "{$prefix}:$key";
                        }
                        if (static::$importSettingsFromXml
                            && !in_array($key, static::$attributeNames)
                        ) {
                            static::$namespacedProperties[$key] = $prefix;
                            static::$attributeNames[] = $key;
                        }
                        $r[$key] = static::setType((string)$value);
                    }
                }
                $children = $xml->children($ns);
                foreach ($children as $key => $value) {
                    if (static::$importSettingsFromXml)
                        static::$namespacedProperties[$key] = $prefix;
                    if (isset($r[$key])) {
                        if (is_array($r[$key]) && $r[$key] != array_values($r[$key])) {
                            $r[$key] = array($r[$key]);
                        } else {
                            $r[$key] = array($r[$key]);
                        }
                        $r[$key][] = $this->read($value, $namespaces);
                    } else {
                        $r[$key] = $this->read($value, $namespaces);
                    }
                }
            }
        }

        if (empty($text)) {
            if (empty($r)) return null;
        } else {
            empty($r)
                ? $r = static::setType($text)
                : (static::$parseTextNodeAsProperty
                ? $r[static::$textNodeName] = static::setType($text)
                : $r[] = static::setType($text));
        }
        return $r;
    }

    public static function setType($value)
    {
        if (empty($value))
            return null;
        if ($value == 'true')
            return true;
        if ($value == 'false')
            return true;
        return $value;
    }
}

