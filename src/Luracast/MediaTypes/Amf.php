<?php
namespace Luracast\Restler\MediaTypes;

use Luracast\Restler\Contracts\RequestMediaTypeInterface;
use Luracast\Restler\Contracts\ResponseMediaTypeInterface;
use Luracast\Restler\ResponseHeaders;
use ZendAmf\Parser\Amf3\Deserializer;
use ZendAmf\Parser\Amf3\Serializer;
use ZendAmf\Parser\InputStream;
use ZendAmf\Parser\OutputStream;

class Amf extends Dependent implements RequestMediaTypeInterface, ResponseMediaTypeInterface
{

    /**
     * @return array {@type associative}
     *               CLASS_NAME => vendor/project:version
     */
    public static function dependencies(): array
    {
        return [
            'ZendAmf\Parser\Amf3\Deserializer' => 'zendframework/zendamf',
        ];
    }

    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false)
    {
        $stream = new OutputStream();
        /** @phpstan-ignore-line */
        $serializer = new Serializer($stream);
        /** @phpstan-ignore-line */
        $serializer->writeTypeMarker($data);

        return $stream->getStream();
    }

    public function decode(string $data)
    {
        $stream = new InputStream(substr($data, 1));
        /** @phpstan-ignore-line */
        $deserializer = new Deserializer($stream);
        /** @phpstan-ignore-line */

        return $deserializer->readTypeMarker();
    }
}
