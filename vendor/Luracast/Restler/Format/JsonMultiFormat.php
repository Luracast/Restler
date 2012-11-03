<?php
namespace Luracast\Restler\Format;

/**
 * Javascript Object Notation Format for Restler Framework.
 *
 * @category   Framework
 * @package    Restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc2
 */
use Luracast\Restler\Format\JsonFormat;

class JsonMultiFormat extends MultiFormat
{
    /**
     * @var string  JSONP callback
     */
    public static $jsonpCallback = 'parseResponse';
	
    /**
     * @var mixed  JSONP metadata array, false to disable the feature
     */
    public static $jsonpMetaData = false;

	/**
	 * @var string  Default MIME
	 */
    public static $mime = 'application/json';

	/**
	 * @var string  Default extension
	 */
    public static $extension = 'json';

    const MIME = 'application/json,text/javascript';
    const EXTENSION = 'json,js';

    public function __construct() {
		$this->jsonFormat = new JsonFormat();

        if (isset ( $_GET['callback'] )) {
            self::$jsonpCallback = $_GET['callback'];
        }
    }

    public function encode($data, $humanReadable = false)
    {
		$isJsonp = true;
		if (static::$mime == 'application/json' || static::$extension == 'json') {
			$isJsonp = false;
		}

		if ( $isJsonp && static::$jsonpMetaData ) {
			$data = array(
				'meta' => static::$jsonpMetaData,
				'data' => $data
			);
		}
		
		// if we request a JSONP response
		if ($isJsonp) {
			return self::$jsonpCallback . '(' . $this->jsonFormat->encode( $data, $human_readable ) . ');';
		}
		// otherwise, send a JSON response
        return $this->jsonFormat->encode( $data, $human_readable );
    }

    public function decode($data)
    {
        return $this->jsonFormat->decode($data);
    }

    /**
     * Set the selected file extension
     *
     * @param string $extension file extension
     */
    public function setExtension($extension)
    {
        static::$extension = $extension;
		static::$mime = array_search($extension, $this->getMIMEMap());
    }
}
