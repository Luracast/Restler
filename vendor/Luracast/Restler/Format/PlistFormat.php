<?php
namespace Luracast\Restler\Format;

/**
 * Plist Format for Restler Framework.
 * Plist is the native data exchange format for Apple iOS and Mac platform.
 * Use this format to talk to mac applications and iOS devices.
 * This class is capable of serving both xml plist and binary plist.
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
use Luracast\Restler\RestlerHelper;
use CFPropertyList\CFTypeDetector;
use CFPropertyList\CFPropertyList;

class PlistFormat implements iFormat
{
    public static $binary_mode = false;
    const MIME_BINARY = 'application/x-plist';
    const EXTENSION_BINARY = 'bplist';
    const MIME_XML = 'application/xml';
    const EXTENSION_XML = 'plist';

    public function getMIMEMap()
    {
        return array (
                self::EXTENSION_BINARY => self::MIME_BINARY,
                self::EXTENSION_XML => self::MIME_XML
        );
    }

    public function getMIME()
    {
        return self::$binary_mode
                ? self::MIME_BINARY
                : self::MIME_XML;
    }

    public function getExtension()
    {
        return self::$binary_mode
                ? self::EXTENSION_BINARY
                : self::EXTENSION_XML;
    }

    public function setMIME($mime)
    {
        self::$binary_mode = $mime == self::MIME_BINARY;
    }

    public function setExtension($extension)
    {
        self::$binary_mode = $extension == self::EXTENSION_BINARY;
    }

    /**
     * Encode the given data in plist format
     *
     * @param array $data
     *            resulting data that needs to
     *            be encoded in plist format
     * @param boolean $humanReadable
     *            set to true when restler
     *            is not running in production mode. Formatter has to
     *            make the encoded output more human readable
     * @return string encoded string
     */
    public function encode($data, $humanReadable = false)
    {
        //require_once 'CFPropertyList.php';
        if (! self::$binary_mode) {
            self::$binary_mode = ! $humanReadable;
        }
        /**
         *
         * @var CFPropertyList
         */
        $plist = new CFPropertyList ();
        $td = new CFTypeDetector ();
        $guessedStructure = $td->toCFType (
            RestlerHelper::objectToArray ( $data )
        );
        $plist->add ( $guessedStructure );

        return self::$binary_mode
            ? $plist->toBinary ()
            : $plist->toXML ( true );
    }

    /**
     * Decode the given data from plist format
     *
     * @param string $data
     *            data sent from client to
     *            the api in the given format.
     * @return array associative array of the parsed data
     */
    public function decode($data)
    {
        //require_once 'CFPropertyList.php';
        $plist = new CFPropertyList ();
        $plist->parse ( $data );

        return $plist->toArray ();
    }

    public function __toString()
    {
        return $this->getExtension ();
    }

    public function setCharset($charset)
    {
        // TODO Auto-generated method stub
    }

    public function getCharset()
    {
        // TODO Auto-generated method stub
    }
}
