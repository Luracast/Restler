<?php

namespace Luracast\Restler\Util;

class TypeParser
{
	const COLLECTION_PATTERN = "/^([a-zA-Z_]{1}[a-zA-Z0-9_]*)\[\]$/i";
	const TYPE_ARRAY = "Array";
	const DEFAULT_ITEM_TYPE = "string";

	static function isCollectionType($type)
	{
		return preg_match(static::COLLECTION_PATTERN, $type) > 0;
	}

	static function parseCollectionType($type)
	{
		if (preg_match(static::COLLECTION_PATTERN, $type, $matches)) {
			return array(
				'type' => static::TYPE_ARRAY,
				'item' => array(
					'type' => $matches[1]
				)
			);
		} elseif (strtolower($type) === strtolower(static::TYPE_ARRAY)) {
			return array(
				'type' => static::TYPE_ARRAY,
				'item' => array(
					'type' => static::DEFAULT_ITEM_TYPE
				)
			);
		}

		return array();
	}
}