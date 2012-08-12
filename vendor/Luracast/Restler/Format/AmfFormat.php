<?php
namespace Luracast\Restler\Format;

/**
 * AMF Binary Format for Restler Framework.
 * Native format supported by Adobe Flash and Adobe AIR
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
use ZendAmf\Parser\OutputStream;
use ZendAmf\Parser\InputStream;
use ZendAmf\Parser\Amf3\Serializer;
use ZendAmf\Parser\Amf3\Deserializer;

class AmfFormat implements iFormat
{
    const PATH = '/Zend/Amf/Parse/';
    const MIME = 'application/x-amf';
    const EXTENSION = 'amf';
    public $path = '';

    public function init()
    {
//        $this->path = dirname ( __FILE__ );
//        $include_path = get_include_path ();
//        if (strpos ( $include_path, 'Zend' ) === FALSE) {
//            set_include_path ( $this->path . ':' . $include_path );
//        }
//        $this->path .= AmfFormat::PATH;
    }

    public function getMIMEMap()
    {
        return array (
                AmfFormat::EXTENSION => AmfFormat::MIME
        );
    }

    public function getMIME()
    {
        return AmfFormat::MIME;
    }

    public function getExtension()
    {
        return AmfFormat::EXTENSION;
    }

    public function setMIME($mime)
    {
        // do nothing
    }

    public function setExtension($extension)
    {
        // do nothing
    }

    public function encode($data, $humanReadable = false)
    {
//		$this->init ();
//		require_once $this->path . 'OutputStream.php';
//		require_once $this->path . 'Amf3/Serializer.php';
        $stream = new OutputStream ();
        $serializer = new Serializer ( $stream );
        $serializer->writeTypeMarker ( $data );

        return $stream->getStream ();
    }

    public function decode($data)
    {
//		$this->init ();
//		require_once $this->path . 'InputStream.php';
//		require_once $this->path . 'Amf3/Deserializer.php';
        $stream = new InputStream ( substr ( $data, 1 ) );
        $deserializer = new Deserializer ( $stream );

        return $deserializer->readTypeMarker ();
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
