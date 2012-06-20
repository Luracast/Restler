<?php
// TODO: define JSON_BIGINT_AS_STRING, JSON_PRETTY_PRINT,
// JSON_UNESCAPED_SLASHES,
// and JSON_UNESCAPED_UNICODE if not defined (PHP version <5.4) and handle the
// options manually to get the same result
/**
 * Javascript Object Notation Format
 *
 * @category Framework
 * @package restler
 * @subpackage format
 * @author R.Arul Kumaran <arul@luracast.com>
 * @copyright 2010 Luracast
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link http://luracast.com/products/restler/
 */
class JsonFormat implements iFormat {
	/**
	 * options that you want to pass for json_encode (used internally)
	 * just make sure those options are supported by your PHP version
	 *
	 * @example JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	 * @var int
	 */
	public static $encodeOptions = 0;
	const MIME = 'application/json';
	const EXTENSION = 'json';

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
		$customHumanReadable = TRUE;
		if ($humanReadable && defined ( 'JSON_PRETTY_PRINT' )) {
			// PHP >= 5.4
			self::$encodeOptions = self::$encodeOptions | JSON_PRETTY_PRINT;
			$customHumanReadable = FALSE;
		}
		$result = json_encode ( RestlerHelper::objectToArray ( $data ), self::$encodeOptions );
		if ($humanReadable && $customHumanReadable)
			$result = $this->json_format ( $result );
			// TODO: modify below line. it is added for JSON_UNESCAPED_SLASHES
		$result = str_replace ( '\/', '/', $result );
		return $result;
	}

	public function decode($data) {
		$decoded = json_decode ( $data );
		if (function_exists ( 'json_last_error' )) {
			$message = '';
			switch (json_last_error ()) {
				case JSON_ERROR_NONE :
					return RestlerHelper::objectToArray ( $decoded );
					break;
				case JSON_ERROR_DEPTH :
					$message = 'maximum stack depth exceeded';
					break;
				case JSON_ERROR_STATE_MISMATCH :
					$message = 'underflow or the modes mismatch';
					break;
				case JSON_ERROR_CTRL_CHAR :
					$message = 'unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX :
					$message = 'malformed JSON';
					break;
				case JSON_ERROR_UTF8 :
					$message = 'malformed UTF-8 characters, possibly incorrectly encoded';
					break;
				default :
					$message = 'unknown error';
					break;
			}
			throw new RestException ( 400, 'Error parsing JSON, ' . $message );
		} else if (strlen ( $data ) && $decoded === NULL || $decoded === $data) {
			throw new RestException ( 400, 'Error parsing JSON' );
		}
		return RestlerHelper::objectToArray ( $decoded );
	}

	public function setCharset($charset) {
	}

	public function getCharset() {
	}

	/**
	 * Pretty print JSON string
	 *
	 * @param string $json        	
	 * @return string formated json
	 */
	private function json_format($json) {
		$tab = '  ';
		$newJson = '';
		$indentLevel = 0;
		$inString = FALSE;
		$len = strlen ( $json );
		for($c = 0; $c < $len; $c ++) {
			$char = $json [$c];
			switch ($char) {
				case '{' :
				case '[' :
					if (! $inString) {
						$newJson .= $char . "\n" . str_repeat ( $tab, $indentLevel + 1 );
						$indentLevel ++;
					} else {
						$newJson .= $char;
					}
					break;
				case '}' :
				case ']' :
					if (! $inString) {
						$indentLevel --;
						$newJson .= "\n" . str_repeat ( $tab, $indentLevel ) . $char;
					} else {
						$newJson .= $char;
					}
					break;
				case ',' :
					if (! $inString) {
						$newJson .= ",\n" . str_repeat ( $tab, $indentLevel );
					} else {
						$newJson .= $char;
					}
					break;
				case ':' :
					if (! $inString) {
						$newJson .= ': ';
					} else {
						$newJson .= $char;
					}
					break;
				case '"' :
					if ($c == 0) {
						$inString = TRUE;
					} elseif ($c > 0 && $json [$c - 1] != '\\') {
						$inString = ! $inString;
					}
				default :
					$newJson .= $char;
					break;
			}
		}
		return $newJson;
	}

	public function __toString() {
		return $this->getExtension ();
	}
}
