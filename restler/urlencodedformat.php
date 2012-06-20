<?php
/**
 * URL Encoded String Format
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class UrlEncodedFormat implements iFormat {
	const MIME = 'application/x-www-form-urlencoded';
	const EXTENSION = 'post';

	public function getMIMEMap() {
		return array (
				self::EXTENSION => self::MIME 
		);
	}

	public function getMIME() {
		return self::MIME;
	}

	public function getExtension() {
		return self::EXTENSION;
	}

	public function setMIME($mime) {
		// do nothing
	}

	public function setExtension($extension) {
		// do nothing
	}

	public function encode($data, $humanReadable = FALSE) {
		return http_build_query ( $data );
	}

	public function decode($data) {
		parse_str ( $data, $r );
		return $r;
	}

	public function __toString() {
		return $this->getExtension ();
	}

	public function setCharset($charset) {
	}

	public function getCharset() {
	}
}