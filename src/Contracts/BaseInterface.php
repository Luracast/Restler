<?php

namespace Luracast\Restler\Contracts;

use Luracast\Restler\Data\Route;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\RestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Used only to quickly create a skeleton base app
 *
 * Interface BaseInterface
 * @package Luracast\Restler\Contracts
 */
interface BaseInterface
{
    public function get(): void;

    public function getPath(string $path): string;

    public function getQuery(array $get = []): array;

    public function getRequestMediaType(string $contentType): RequestMediaTypeInterface;

    public function getBody(string $raw = ''): array;


    public function route(): void;


    public function negotiate(): void;

    public function negotiateResponseMediaType(string $path, string $acceptHeader = ''): ResponseMediaTypeInterface;

    public function negotiateCORS(
        string $requestMethod,
        string $accessControlRequestMethod = '',
        string $accessControlRequestHeaders = '',
        string $origin = ''
    ): void;

    public function negotiateCharset(string $acceptCharset = '*'): void;

    public function negotiateLanguage(string $acceptLanguage = ''): void;


    public function authenticate();


    public function validate();


    public function call();


    public function compose($response = null);

    public function composeHeaders(?Route $info, string $origin = '', HttpException $e = null): void;

    public function message(Throwable $e);


    public function respond($response = []): ResponseInterface;

    public function stream($data): ResponseInterface;


    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $rawRequestBody = ''
    ): ResponseInterface;
}
