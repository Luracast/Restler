<?php

namespace Luracast\Restler\MediaTypes;


use Luracast\Restler\Contracts\RequestMediaTypeInterface;
use Luracast\Restler\Contracts\ResponseMediaTypeInterface;
use Luracast\Restler\ResponseHeaders;
use Luracast\Restler\Utils\Convert;
use Psr\Http\Message\ServerRequestInterface;

class UrlEncoded extends MediaType implements RequestMediaTypeInterface, ResponseMediaTypeInterface
{

    public const MIME = 'application/x-www-form-urlencoded';
    public const EXTENSION = 'post';
    private \Psr\Http\Message\ServerRequestInterface $request;

    public function __construct(Convert $convert, ServerRequestInterface $request)
    {
        parent::__construct($convert);
        $this->request = $request;
    }

    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false)
    {
        return http_build_query(static::encoderTypeFix($data));
    }

    public function decode(string $data): array
    {
        $r = $this->request->getParsedBody();
        if (empty($r)) {
            parse_str($data, $r);
        }
        return self::decoderTypeFix($r);
    }

    public static function encoderTypeFix(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_bool($v)) {
                $data[$k] = $v = $v ? 'true' : 'false';
            } elseif (is_array($v)) {
                $data[$k] = $v = static::decoderTypeFix($v);
            }
        }
        return $data;
    }

    public static function decoderTypeFix(array $data): array
    {
        foreach ($data as $k => $v) {
            if ($v === 'true' || $v === 'false') {
                $data[$k] = $v = $v === 'true';
            } elseif (is_array($v)) {
                $data[$k] = $v = static::decoderTypeFix($v);
            }
        }
        return $data;
    }
}
