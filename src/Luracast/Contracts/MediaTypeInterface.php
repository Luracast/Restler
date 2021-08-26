<?php
namespace Luracast\Restler\Contracts;


interface MediaTypeInterface
{
    /**
     * Get MIME type => Extension mappings as an associative array
     *
     * @return array list of mime strings for the format
     * @example array('application/json'=>'json');
     */
    public static function supportedMediaTypes(): array;

    public function mediaType(string $type = null);

    public function charset(string $charset = null);

    public function extension(string $extension = null);
}
