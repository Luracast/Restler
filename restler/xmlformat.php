<?php
/**
 * XML Markup Format for Restler Framework
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class XmlFormat implements iFormat
{
	public static $parse_attributes=true;
	public static $parse_namespaces=false;
	public static $attribute_names=array('xmlns');
	/**
	 * Default name for the root node.
	 * @var string $rootNodeName
	 */
	public static  $root_name='response';
	public static  $default_tag_name='item';

	const MIME ='application/xml';
	const EXTENSION = 'xml';

	public function getMIMEMap()
	{
		return array(self::EXTENSION=>self::MIME);
	}
	public function getMIME(){
		return  self::MIME;
	}
	public function getExtension(){
		return self::EXTENSION;
	}
	public function setMIME($mime){
		//do nothing
	}
	public function setExtension($extension){
		//do nothing
	}

	public function encode($data, $human_readable=false){
		return $this->toXML( object_to_array($data, false),
		self::$root_name, $human_readable);
	}

	public function decode($data){
		try {
			if($data=='')return array();
			return $this->toArray($data);
		} catch (Exception $e) {
			throw new RestException(400, "Error decoding request. ".
			$e->getMessage());
		}
	}

	public function __toString(){
		return $this->getExtension();
	}

	/**
	 * determine if a variable is an associative array
	 */
	public function isAssoc( $array ) {
		return (is_array($array) && 0 !== count(array_diff_key($array,
		array_keys(array_keys($array)))));
	}

	/**
	 * The main function for converting to an XML document.
	 * Pass in a multi dimensional array and this recrusively loops through
	 * and builds up an XML document.
	 * @param array $data
	 * @param string $rootNodeName - what you want the root node to be -
	 * defaults to data.
	 * @param SimpleXMLElement $xml - should only be used recursively
	 * @return string XML
	 * @link http://bit.ly/n85yLi
	 */
	public function toXML( $data, $root_node_name = 'result',
	$human_readable=false, &$xml=null) {
		// turn off compatibility mode as simple xml
		//throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1)
		ini_set ('zend.ze1_compatibility_mode', 0);
		if (is_null($xml))
		$xml = @simplexml_load_string("<$root_node_name/>");
		if(is_array($data)){
			$numeric=0;
			// loop through the data passed in.
			foreach( $data as $key => $value ) {

				// no numeric keys in our xml please!
				if ( is_numeric( $key ) ) {
					$numeric = 1;
					$key = self::$root_name == $root_node_name ?
					self::$default_tag_name : $root_node_name;
				}

				// delete any char not allowed in XML element names
				$key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

				// if there is another array found recrusively
				//call this function
				if ( is_array( $value ) ) {
					$node = $this->isAssoc( $value ) || $numeric ?
					$xml->addChild( $key ) : $xml;

					// recrusive call.
					if ( $numeric ) $key = 'anon';
					$this->toXML($value, $key, $human_readable, $node);
				} else {
					// add single node or attribute
					$value=htmlspecialchars($value);
					in_array($key,self::$attribute_names) ?
					$xml->addAttribute($key,$value) :
					$xml->addChild( $key, $value);
				}
			}
		}else{ //if given data is a string or number
			//simply wrap it as text node to root
			if(is_bool($data)) $data = $data ? 'true' : 'false';
			$xml = @simplexml_load_string(
			"<$root_node_name>".htmlspecialchars($data)."</$root_node_name>");
			//$xml->{0} = $data;
		}
		if(!$human_readable){
			return $xml->asXML();
		}else{
			$dom = dom_import_simplexml($xml)->ownerDocument;
			$dom->formatOutput = true;
			return $dom->saveXML();
		}
	}

	/**
	 * Convert an XML document to a multi dimensional array
	 * Pass in an XML document (or SimpleXMLElement object) and this
	 * recrusively loops through and builds a representative array
	 *
	 * @param string $xml - XML document - can optionally be a
	 * SimpleXMLElement object
	 * @return array ARRAY
	 * @link http://bit.ly/n85yLi
	 */
	public function toArray( $xml, $firstCall=true) {
		if ( is_string( $xml ) ) $xml = new SimpleXMLElement( $xml );
		$children = $xml->children();
		if ( !$children ) {
			$r = (string) $xml;
			if($r=='true' || $r=='false')$r=$r=='true';
			return $r;
		}
		$arr = array();

		if($firstCall){
			//reset the attribute names list
			self::$attribute_names=array();
			self::$root_name = $xml->getName();
			if (self::$parse_namespaces){
				foreach($xml->getDocNamespaces(TRUE) as $namepace => $uri) {
					$arr[$namepace=='' ? 'xmlns' :
					'xmlns:'.$namepace] = (string)$uri;
				}
			}
		}
		if(self::$parse_attributes){
			foreach($xml->attributes() as $attName => $attValue) {
				$arr[$attName] = (string)$attValue;
				//add to attribute list for round trip support
				self::$attribute_names[]=$attName;
			}
		}
		foreach ($children as $key => $node) {
			$node = $this->toArray($node, false);
			// support for 'anon' non-associative arrays
			if ($key == 'anon') $key = count($arr);

			// if the node is already set, put it into an array
			if (isset($arr[$key])) {
				if ( !is_array($arr[$key]) || @$arr[$key][0] == null )
				$arr[$key] = array($arr[$key]);
				$arr[$key][] = $node;
			} else {
				$arr[$key] = $node;
			}
		}
		return $arr;
	}

	/**
	 * When you decode an XML its structure is copied to the static vars
	 * we can use this function to echo them out and then copy paste inside
	 * our service methods
	 * @return string PHP source code to reproduce the configuration
	 */
	public static function exportCurrentSettings() {
		$s = 'self::$root_name = "'.
		(self::$root_name)."\";\n";
		$s .= 'self::$attribute_names = '.
		(var_export(self::$attribute_names, true)).";\n";
		$s .= 'self::$default_tag_name = "'.
		self::$default_tag_name."\";\n";
		$s .= 'self::$parse_attributes = '.
		(self::$parse_attributes ? 'true' : 'false').";\n";
		$s .= 'self::$parse_namespaces = '.
		(self::$parse_namespaces ? 'true' : 'false').";\n\n\n";
		return $s;
	}

}
