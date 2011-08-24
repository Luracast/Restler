<?php
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

class AmfFormat implements iFormat
{
	const PATH = '/Zend/Amf/Parse/';
	const MIME = 'application/x-amf';
	const EXTENSION = 'amf';

	public $path = '';

	public function init(){
		$this->path = dirname(__FILE__);
		$include_path = get_include_path();
		if(strpos($include_path, 'Zend')===FALSE){
			set_include_path($this->path.':'.$include_path);
		}
		$this->path .= AmfFormat::PATH;
	}

	public function getMIMEMap()
	{
		return array(AmfFormat::EXTENSION=>AmfFormat::MIME);
	}
	public function getMIME(){
		return  AmfFormat::MIME;
	}
	public function getExtension(){
		return AmfFormat::EXTENSION;
	}
	public function setMIME($mime){
		//do nothing
	}
	public function setExtension($extension){
		//do nothing
	}

	public function encode($data, $human_readable=false){
		$this->init();
		require_once $this->path . 'OutputStream.php';
		require_once $this->path . 'Amf3/Serializer.php';
		$stream = new Zend_Amf_Parse_OutputStream();
		$serializer = new Zend_Amf_Parse_Amf3_Serializer($stream);
		$serializer->writeTypeMarker($data);
		return $stream->getStream();
	}

	public function decode($data){
		$this->init();
		require_once $this->path .'InputStream.php';
		require_once $this->path .'Amf3/Deserializer.php';
		$stream = new Zend_Amf_Parse_InputStream(substr($data, 1));
		$deserializer = new Zend_Amf_Parse_Amf3_Deserializer($stream);
		return $deserializer->readTypeMarker();
	}

	public function __toString(){
		return $this->getExtension();
	}
}