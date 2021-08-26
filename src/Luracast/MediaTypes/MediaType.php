<?php

namespace Luracast\Restler\MediaTypes;

use Luracast\Restler\Contracts\MediaTypeInterface;
use Luracast\Restler\Contracts\SelectivePathsInterface;
use Luracast\Restler\Contracts\SelectivePathsTrait;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Utils\Convert;
use Luracast\Restler\Utils\Text;

abstract class MediaType implements MediaTypeInterface, SelectivePathsInterface
{
    use SelectivePathsTrait;

    /**
     * override in the extending class
     */
    public const MIME = 'text/plain';
    /**
     * override in the extending class
     */
    public const EXTENSION = 'txt';

    protected ?string $mime = null;
    protected ?string $extension = null;
    protected string $charset = 'utf-8';
    protected Convert $convert;

    public function __construct(Convert $convert)
    {
        $this->convert = $convert;
    }

    /**
     * @throws HttpException
     */
    public function mediaType(string $type = null)
    {
        if (is_null($type)) {
            return $this->mime ?: static::MIME;
        }
        $types = static::supportedMediaTypes();
        if (isset($types[$type])) {
            $this->mime = $type;
            $this->extension = $types[$type];
            return $this;
        }
        //support for vendor MIME types
        foreach ($types as $mime => $extension) {
            if (Text::endsWith($type, "+$extension")) {
                $this->mime = $type;
                $this->extension = $extension;
                return $this;
            }
        }
        throw new HttpException(500, "Invalid Media Type `$type`");
    }

    /**
     * Get MIME type => Extension mappings as an associative array
     *
     * @return array list of mime strings for the MediaType
     * @example array('application/json'=>'json');
     */
    public static function supportedMediaTypes(): array
    {
        return [static::MIME => static::EXTENSION];
    }

    /**
     * @throws HttpException
     */
    public function extension(string $extension = null)
    {
        if (is_null($extension)) {
            return $this->extension ?: static::EXTENSION;
        }
        if ($mime = array_search($extension, static::supportedMediaTypes())) {
            $this->mime = $mime;
            $this->extension = $extension;
            return $this;
        }
        throw new HttpException(500, "Invalid Extension `$extension`");
    }

    public function charset(string $charset = null)
    {
        if (is_null($charset)) {
            return $this->charset;
        }
        $this->charset = $charset;
        return $this;
    }
}
