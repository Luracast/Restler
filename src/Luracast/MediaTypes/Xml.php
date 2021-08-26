<?php
namespace Luracast\Restler\MediaTypes;


use Exception;
use Luracast\Restler\Contracts\RequestMediaTypeInterface;
use Luracast\Restler\Contracts\ResponseMediaTypeInterface;
use Luracast\Restler\ResponseHeaders;
use RuntimeException;
use SimpleXMLElement;
use XMLWriter;

class Xml extends MediaType implements RequestMediaTypeInterface, ResponseMediaTypeInterface
{
    public const MIME = 'application/xml';
    public const EXTENSION = 'xml';

    // ==================================================================
    //
    // Properties related to reading/parsing/decoding xml
    //
    // ------------------------------------------------------------------
    public static bool $importSettingsFromXml = false;
    public static bool $parseAttributes = true;
    public static bool $parseNamespaces = true;
    public static bool $parseTextNodeAsProperty = true;

    // ==================================================================
    //
    // Properties related to writing/encoding xml
    //
    // ------------------------------------------------------------------
    public static bool $useTextNodeProperty = true;
    public static bool $useNamespaces = true;
    public static array $cdataNames = [];

    // ==================================================================
    //
    // Common Properties
    //
    // ------------------------------------------------------------------
    public static array $attributeNames = [];
    public static string $textNodeName = 'text';
    public static array $namespaces = [];
    public static array $namespacedProperties = [];
    /**
     * Default name for the root node.
     */
    public static string $rootName = 'response';
    public static string $defaultTagName = 'item';

    /**
     * When you decode an XML its structure is copied to the static vars
     * we can use this function to echo them out and then copy paste inside
     * our service methods
     *
     * @return string PHP source code to reproduce the configuration
     */
    public static function exportCurrentSettings(): string
    {
        $s = 'XmlMediaType::$rootName = "' . (self::$rootName) . "\";\n";
        $s .= 'XmlMediaType::$attributeNames = ' .
            (var_export(self::$attributeNames, true)) . ";\n";
        $s .= 'XmlMediaType::$defaultTagName = "' .
            self::$defaultTagName . "\";\n";
        $s .= 'XmlMediaType::$parseAttributes = ' .
            (self::$parseAttributes ? 'true' : 'false') . ";\n";
        $s .= 'XmlMediaType::$parseNamespaces = ' .
            (self::$parseNamespaces ? 'true' : 'false') . ";\n";
        if (self::$parseNamespaces) {
            $s .= 'XmlMediaType::$namespaces = ' .
                (var_export(self::$namespaces, true)) . ";\n";
            $s .= 'XmlMediaType::$namespacedProperties = ' .
                (var_export(self::$namespacedProperties, true)) . ";\n";
        }

        return $s;
    }

    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false)
    {
        $data = $this->convert->toArray($data);
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', $this->charset);
        if ($humanReadable) {
            $xml->setIndent(true);
            $xml->setIndentString('    ');
        }
        static::$useNamespaces && isset(static::$namespacedProperties[static::$rootName])
            ?
            $xml->startElementNs(
                static::$namespacedProperties[static::$rootName],
                static::$rootName,
                static::$namespaces[static::$namespacedProperties[static::$rootName]]
            )
            :
            $xml->startElement(static::$rootName);
        if (static::$useNamespaces) {
            foreach (static::$namespaces as $prefix => $ns) {
                if (isset(static::$namespacedProperties[static::$rootName])
                    && static::$namespacedProperties[static::$rootName] == $prefix
                ) {
                    continue;
                }
                $prefix = 'xmlns' . (empty($prefix) ? '' : ':' . $prefix);
                $xml->writeAttribute($prefix, $ns);
            }
        }
        $this->write($xml, $data, static::$rootName);
        $xml->endElement();

        return $xml->outputMemory();
    }

    public function write(XMLWriter $xml, $data, $parent): void
    {
        $text = [];
        if (is_array($data)) {
            if (static::$useTextNodeProperty && isset($data[static::$textNodeName])) {
                $text [] = $data[static::$textNodeName];
                unset($data[static::$textNodeName]);
            }
            $attributes = array_flip(static::$attributeNames);
            //make sure we deal with attributes first
            $temp = [];
            foreach ($data as $key => $value) {
                if (isset($attributes[$key])) {
                    $temp[$key] = $data[$key];
                    unset($data[$key]);
                }
            }
            $data = array_merge($temp, $data);
            foreach ($data as $key => $value) {
                if (is_numeric($key)) {
                    if (!is_array($value)) {
                        $text [] = $value;
                        continue;
                    }
                    $key = static::$defaultTagName;
                }
                $useNS = static::$useNamespaces
                    && !empty(static::$namespacedProperties[$key])
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
                if (isset($attributes[$key])) {
                    $xml->writeAttribute($useNS ? static::$namespacedProperties[$key] . ':' . $key : $key, $value);
                } else {
                    $useNS
                        ?
                        $xml->startElementNs(
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
            $text [] = (string)$data;
        }
        if (!empty($text)) {
            if (count($text) == 1) {
                in_array($parent, static::$cdataNames)
                    ? $xml->writeCdata(implode('', $text))
                    : $xml->text(implode('', $text));
            } else {
                foreach ($text as $t) {
                    $xml->writeElement(static::$textNodeName, $t);
                }
            }
        }
    }

    public function decode(string $data)
    {
        try {
            if ($data == '') {
                return [];
            }
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string(
                $data,
                "SimpleXMLElement",
                LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_COMPACT
            );
            if (false === $xml) {
                $error = libxml_get_last_error();
                throw new Exception(
                    400, 'Malformed XML. '
                       . trim($error->message, "\r\n") . ' at line ' . $error->line
                );
            }
            libxml_clear_errors();
            if (static::$importSettingsFromXml) {
                static::$attributeNames = [];
                static::$namespacedProperties = [];
                static::$namespaces = [];
                static::$rootName = $xml->getName();
                $namespaces = $xml->getNamespaces();
                if (count($namespaces)) {
                    $p = strpos($data, $xml->getName());
                    if ($p && $data[$p - 1] == ':') {
                        $s = strpos($data, '<') + 1;
                        $prefix = substr($data, $s, $p - $s - 1);
                        static::$namespacedProperties[static::$rootName] = $prefix;
                    }
                }
            }
            $data = $this->read($xml);
            if (count($data) == 1 && isset($data[static::$textNodeName])) {
                $data = $data[static::$textNodeName];
            }

            return $data;
        } catch (RuntimeException $e) {
            throw new Exception(
                400,
                "Error decoding request. " . $e->getMessage()
            );
        }
    }

    public function read(SimpleXMLElement $xml, $namespaces = null)
    {
        $r = [];
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
                if (is_array($r[$key])) {
                    if ($r[$key] != array_values($r[$key])) {
                        $r[$key] = [$r[$key]];
                    }
                } else {
                    $r[$key] = [$r[$key]];
                }
                $r[$key][] = $this->read($value, $namespaces);
            } else {
                $r[$key] = $this->read($value);
            }
        }

        if (static::$parseNamespaces) {
            if (is_null($namespaces)) {
                $namespaces = $xml->getDocNamespaces(true);
            }
            foreach ($namespaces as $prefix => $ns) {
                static::$namespaces[$prefix] = $ns;
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
                    if (static::$importSettingsFromXml) {
                        static::$namespacedProperties[$key] = $prefix;
                    }
                    if (isset($r[$key])) {
                        if (is_array($r[$key])) {
                            if ($r[$key] != array_values($r[$key])) {
                                $r[$key] = [$r[$key]];
                            }
                        } else {
                            $r[$key] = [$r[$key]];
                        }
                        $r[$key][] = $this->read($value, $namespaces);
                    } else {
                        $r[$key] = $this->read($value, $namespaces);
                    }
                }
            }
        }

        if (empty($text) && $text !== '0') {
            if (empty($r)) {
                return null;
            }
        } else {
            empty($r)
                ? $r = static::setType($text)
                : (
            static::$parseTextNodeAsProperty
                ? $r[static::$textNodeName] = static::setType($text)
                : $r[] = static::setType($text)
            );
        }

        return $r;
    }

    public static function setType($value)
    {
        if (empty($value) && $value !== '0') {
            return null;
        }
        if ($value == 'true') {
            return true;
        }
        if ($value == 'false') {
            return true;
        }

        return $value;
    }
}
