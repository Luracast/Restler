<?php

namespace Luracast\Restler\MediaTypes;


use CFPropertyList\CFPropertyList;
use CFPropertyList\CFTypeDetector;
use Luracast\Restler\Contracts\RequestMediaTypeInterface;
use Luracast\Restler\Contracts\ResponseMediaTypeInterface;
use Luracast\Restler\ResponseHeaders;

class Plist extends Dependent implements RequestMediaTypeInterface, ResponseMediaTypeInterface
{
    public static ?bool $compact = null;

    /**
     * @return array {@type associative}
     *               CLASS_NAME => vendor/project:version
     */
    public static function dependencies(): array
    {
        return [
            'CFPropertyList\CFPropertyList' => 'rodneyrehm/plist:dev-master'
        ];
    }

    public static function supportedMediaTypes(): array
    {
        return [
            'application/xml' => 'plist',
            'application/x-plist' => 'plist',
        ];
    }

    public function mediaType(string $type = null)
    {
        if (!is_null($type)) {
            static::$compact = $type == 'application/x-plist';
        }

        return parent::mediaType($type);
    }

    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false)
    {
        if (!isset(self::$compact)) {
            self::$compact = !$humanReadable;
        }
        $plist = new CFPropertyList ();
        /** @phpstan-ignore-line */
        $td = new CFTypeDetector ();
        /** @phpstan-ignore-line */
        $guessedStructure = $td->toCFType(
            $this->convert->toArray($data)
        );
        $plist->add($guessedStructure);

        return self::$compact
            ? $plist->toBinary()
            : $plist->toXML(true);
    }

    public function decode(string $data)
    {
        $plist = new CFPropertyList ();
        /** @phpstan-ignore-line */
        $plist->parse($data);

        return $plist->toArray();
    }
}
