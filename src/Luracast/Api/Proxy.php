<?php

namespace Luracast\Restler\Api;


use GuzzleHttp\Client;
use GuzzleHttp\DefaultHandler;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Utils\Dump;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

//use Yurun\Util\Swoole\Guzzle\SwooleHandler;

class Proxy
{
    public static $mappings = [
        'google' => 'https://www.google.com/search'
    ];
    private ?ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        //DefaultHandler::setDefaultHandler(SwooleHandler::class);
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
        //die('<pre>' . Dump::request($request));
        /*
        $handler = new CurlHandler(); //new CurlMultiHandler();
        $stack = HandlerStack::create($handler);
        $client = new Client(['handler' => $stack]);
        */
        //go(function () use ($request) {
        $client = new Client();
        echo(Dump::response($client->send($request)));
        return $client->send($request);
        //});
    }
}
