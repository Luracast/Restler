<?php

namespace Luracast\Restler\Api;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use Luracast\Restler\Exceptions\HttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Proxy
{
    public static array $mappings = [
        'google' => 'https://www.google.com/search'
    ];
    private ?ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param $name $name of the configured proxy path
     * @return ResponseInterface
     * @throws GuzzleException
     * @throws HttpException
     * @url GET {name}
     * @url POST {name}
     * @url PUT {name}
     * @url DELETE {name}
     * @url PATCH {name}
     */
    public function index($name): ResponseInterface
    {
        if (!$url = static::$mappings[$name] ?? null) {
            throw new HttpException(404);
        }
        $uri = new Uri($url);
        $newUri = $this->request->getUri()
            ->withPath($uri->getPath())
            ->withHost($uri->getHost())
            ->withPort($uri->getPort());
        $request = $this->request->withUri($newUri);
        return (new Client)->send($request)->withoutHeader('transfer-encoding');
    }
}
