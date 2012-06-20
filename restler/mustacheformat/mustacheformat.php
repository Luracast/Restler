<?php
class MustacheFormat implements iFormat {
	const MIME = 'text/html';
	const EXTENSION = 'mustache';
	/**
	 * Injected at runtime
	 *
	 * @var Restler
	 */
	public $restler;
	public static $template = 'default';

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

	public function encode($data, $humanReadable = FALSE) {
		$data = RestlerHelper::objectToArray ( $data );
		$metadata = $this->restler->serviceMethodInfo->metadata;
		$params = $metadata ['param'];
		foreach ( $params as $index => &$param ) {
			$index = intval ( $index );
			if (is_numeric ( $index )) {
				$param ['value'] = $this->restler->serviceMethodInfo->arguments [$index];
			}
		}
		$data ['param'] = $params;
		if (isset ( $metadata ['template'] )) {
			self::$template = $metadata ['template'];
		}
		$m = new MustacheTemplate ( $this->loadTemplate ( self::$template ), $data );
		return $m->render ();
	}

	protected function loadTemplate($name) {
		return file_get_contents ( $_SERVER ['DOCUMENT_ROOT'] . dirname ( $_SERVER ['SCRIPT_NAME'] ) . '/templates/' . $name . '.htm' );
	}

	public function decode($data) {
		throw new RestException ( 405, 'MustacheFormat is write only' );
	}

	public function __toString() {
		return $this->getExtension ();
	}

	public function setMIME($mime) {
		// do nothing
	}

	public function setExtension($extension) {
		// do nothing
	}

	public function setCharset($charset) {
		// TODO Auto-generated method stub
	}

	public function getCharset() {
		// TODO Auto-generated method stub
	}
}