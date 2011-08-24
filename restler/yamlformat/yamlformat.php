<?php
/**
 * YAML Format for Restler Framework
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class YamlFormat implements iFormat
{
	const MIME ='text/plain';
	const EXTENSION = 'yaml';

	public function getMIMEMap()
	{
		return array(YamlFormat::EXTENSION=>YamlFormat::MIME);
	}
	public function getMIME(){
		return  YamlFormat::MIME;
	}
	public function getExtension(){
		return YamlFormat::EXTENSION;
	}
	public function setMIME($mime){
		//do nothing
	}
	public function setExtension($extension){
		//do nothing
	}

	public function encode($data, $human_readable=false){
		require_once 'sfyaml.php';
		return sfYaml::dump(object_to_array($data));
	}

	public function decode($data){
		require_once 'sfyaml.php';
		return sfYaml::load($data);
	}

	public function __toString(){
		return $this->getExtension();
	}
}