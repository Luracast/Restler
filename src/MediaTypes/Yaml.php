<?php
namespace Luracast\Restler\MediaTypes;


use Luracast\Restler\Contracts\RequestMediaTypeInterface;
use Luracast\Restler\Contracts\ResponseMediaTypeInterface;
use Luracast\Restler\ResponseHeaders;
use Symfony\Component\Yaml\Yaml as Y;

class Yaml extends Dependent implements RequestMediaTypeInterface, ResponseMediaTypeInterface
{
    public const MIME = 'text/plain';
    public const EXTENSION = 'yaml';

    /**
     * @return array {@type associative}
     *               CLASS_NAME => vendor/project:version
     */
    public static function dependencies(): array
    {
        return ['Symfony\Component\Yaml\Yaml' => 'symfony/yaml'];
    }

    public function decode(string $data)
    {
        return Y::parse($data);
    }

    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false)
    {
        return @Y::dump($this->convert->toArray($data));
    }
}
