<?php
namespace Luracast\Restler\Format;

use Luracast\Restler\Data\Object;
use Luracast\Restler\RestException;
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
    public static $importRootNameAndAttributesFromXml = false;
    public static $parseAttributes = true;
    public static $parseNamespaces = true;
    public static $attributeNames = array('xmlns');
    public static $nodeValueName = 'nodeValue';
    public static $nameSpaces = array();
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
            (self::$parseNamespaces ? 'true' : 'false') . ";\n\n\n";
        if (self::$parseNamespaces) {
            $s .= 'XmlFormat::$nameSpaces = ' .
                (var_export(self::$nameSpaces, true)) . ";\n";
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
        $xml->startElement(static::$rootName);
        $this->write($xml, $data);
        $xml->endElement();
        return $xml->outputMemory();
    }

    public function write(XMLWriter $xml, $data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key))
                $key = static::$defaultTagName;
            if (is_array($value)) {
                $xml->startElement($key);
                if(isset($value[static::$nodeValueName])) {
                	$text = $value[static::$nodeValueName];
                	unset($value[static::$nodeValueName]);
                	$this->write($xml, $value);
                	$xml->text($text);
                }
                else {
                	$this->write($xml, $value);
                }
                $xml->endElement();
                continue;
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            in_array($key, static::$attributeNames)
                ? $xml->writeAttribute($key, $value)
                : $xml->writeElement($key, $value);

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
            foreach (libxml_get_errors() as $error) {
                throw new RestException(400, 'Malformed xml at line ' . $error->line);
            }
            libxml_clear_errors();
            $data = array();
            if (static::$parseNamespaces) {
                static::$nameSpaces = $xml->getDocNamespaces(TRUE);
                foreach (static::$nameSpaces as $prefix => $ns) {
                    $data += $this->fix(
                        json_decode(json_encode($xml->children($ns, false)), true)
                    );
                }
            }
            if (static::$importRootNameAndAttributesFromXml) {
                static::$rootName = $xml->getName();
            }
            $data += $this->fix(json_decode(json_encode($xml), true));
            return $data;
        } catch (\RuntimeException $e) {
            throw new RestException(400,
                "Error decoding request. " . $e->getMessage());
        }
    }

    public function fix($data)
    {
        foreach ($data as $key => $value) {
            if ($key == '@attributes') {
                foreach ($value as $att => $v) {
                    if (static::$importRootNameAndAttributesFromXml
                        && !in_array($att, static::$attributeNames)
                    ) {
                        static::$attributeNames[] = $att;
                    }
                    $data[$att] = empty($v) ? null : $v;
                }
                unset($data[$key]);
            } elseif (is_array($value)) {
                $data[$key] = empty($value) ? null : $this->fix($value);
            } elseif ($value == 'true') {
                $data[$key] = $value = true;
            } elseif ($value == 'false') {
                $data[$key] = $value = false;
            }
        }
        return $data;
    }
}

