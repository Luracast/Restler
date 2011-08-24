<?php
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
class PlistFormat implements iFormat
{
	public static $binary_mode = false;

	const MIME_BINARY = 'application/x-plist';
	const MIME_XML = 'application/xml';

	const EXTENSION_BINARY = 'bplist';
	const EXTENSION_XML = 'plist';

	public function getMIMEMap(){
		return array(
		PlistFormat::EXTENSION_BINARY=>PlistFormat::MIME_BINARY,
		PlistFormat::EXTENSION_XML=>PlistFormat::MIME_XML
		);
	}

	public function getMIME(){
		return  PlistFormat::$binary_mode ? PlistFormat::MIME_BINARY : PlistFormat::MIME_XML;
	}
	public function getExtension(){
		return PlistFormat::$binary_mode ? PlistFormat::EXTENSION_BINARY : PlistFormat::EXTENSION_XML;
	}
	public function setMIME($mime){
		PlistFormat::$binary_mode = $mime==PlistFormat::MIME_BINARY;
	}
	public function setExtension($extension){
		PlistFormat::$binary_mode = $extension==PlistFormat::EXTENSION_BINARY;
	}

	/**
	 * Encode the given data in plist format
	 * @param array $data resulting data that needs to
	 * be encoded in plist format
	 * @param boolean $human_readable set to true when restler
	 * is not running in production mode. Formatter has to
	 * make the encoded output more human readable
	 * @return string encoded string
	 */
	public function encode($data, $human_readable=false){
		require_once'CFPropertyList.php';
		if(!PlistFormat::$binary_mode) {
			PlistFormat::$binary_mode = !$human_readable;
		} else {
			$human_readable=false;
		}
		/**
		 * @var CFPropertyList
		 */
		$plist = new CFPropertyList();
		$td = new CFTypeDetector();
		$guessedStructure = $td->toCFType(object_to_array($data));
		$plist->add( $guessedStructure );
		return $human_readable ? $plist->toXML(true) : $plist->toBinary();
	}

	/**
	 * Decode the given data from plist format
	 * @param string $data data sent from client to
	 * the api in the given format.
	 * @return array associative array of the parsed data
	 */
	public function decode($data){
		require_once'CFPropertyList.php';
		$plist = new CFPropertyList();
		$plist->parse($data);
		return $plist->toArray();
	}
	public function __toString(){
		return $this->getExtension();
	}
}