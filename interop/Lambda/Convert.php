<?php
/** @noinspection PhpInternalEntityUsedInspection */


namespace Lambda;


use Bref\Context\Context;
use Bref\Event\Http\HttpRequestEvent;
use Bref\Event\InvalidLambdaEvent;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\UploadedFile;
use Luracast\Restler\Utils\Text;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Riverline\MultiPartParser\Part;
use stdClass;

class Convert
{
    public static array $binaryTypes = [
        'image/png',
        'image/jpeg',
        'image/gif',
    ];

    /**
     * @throws InvalidLambdaEvent
     */
    final public static function toPSR7(array $payload, array $headers): ServerRequestInterface
    {
        $event = new HttpRequestEvent($payload);
        $context = new Context(
            $headers['lambda-runtime-aws-request-id'],
            (int)$headers['lambda-runtime-deadline-ms'],
            $headers['lambda-runtime-invoked-function-arn'],
            $headers['lambda-runtime-trace-id']
        );

        [$files, $parsedBody] = self::parseBodyAndUploadedFiles($event);
        $reqContext = $event->getRequestContext();
        $server = [
            'SERVER_PROTOCOL' => $event->getProtocolVersion(),
            'REQUEST_METHOD' => $event->getMethod(),
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
            'QUERY_STRING' => $event->getQueryString(),
            'DOCUMENT_ROOT' => getcwd(),
            'REQUEST_URI' => $reqContext['path'], //'/dev' . $event->getUri(),
            'SCRIPT_NAME' => '/' . $reqContext['stage'] . '/index.php', //'/dev/index.php'
        ];

        $headers = $event->getHeaders();
        if (isset($headers['Host'])) {
            $server['HTTP_HOST'] = $headers['Host'];
            $server['SERVER_NAME'] = strtok($headers['Host'], ':');
        }

        $uri = sprintf(
            "%s://%s:%s%s",
            $headers['x-forwarded-proto'][0] ?? 'https',
            $event->getServerName(),
            $event->getServerPort(),
            Text::beginsWith($event->getUri(), '/' . $reqContext['stage'])
                ? $event->getUri()
                : $reqContext['path']
        );


        $request = new ServerRequest(
            $event->getMethod(),
            $uri,
            $event->getHeaders(),
            $event->getBody(),
            $event->getProtocolVersion(),
            $server
        );

        return $request->withUploadedFiles($files)
            ->withCookieParams($event->getCookies())
            ->withQueryParams($event->getQueryParameters())
            ->withParsedBody($parsedBody)
            ->withAttribute('lambda-event', $event)
            ->withAttribute('lambda-context', $context);
    }

    final public static function fromPSR7(ResponseInterface $psr7Response, bool $multiHeaders = false): array
    {
        $base64Encoding = in_array(
            $psr7Response->getHeaderLine('Content-Type'),
            static::$binaryTypes
        );
        $body = (string)$psr7Response->getBody();

        $headers = [];
        foreach ($psr7Response->getHeaders() as $name => $values) {
            // Capitalize header keys
            // See https://github.com/zendframework/zend-diactoros/blob/754a2ceb7ab753aafe6e3a70a1fb0370bde8995c/src/Response/SapiEmitterTrait.php#L96
            $name = str_replace('-', ' ', $name);
            $name = ucwords($name);
            $name = str_replace(' ', '-', $name);

            if ($multiHeaders) {
                // Make sure the values are always arrays
                $headers[$name] = is_array($values) ? $values : [$values];
            } else {
                // Make sure the values are never arrays
                $headers[$name] = is_array($values) ? end($values) : $values;
            }
        }

        // The headers must be a JSON object. If the PHP array is empty it is
        // serialized to `[]` (we want `{}`) so we force it to an empty object.
        $headers = empty($headers) ? new stdClass() : $headers;

        // Support for multi-value headers
        $headersKey = $multiHeaders ? 'multiValueHeaders' : 'headers';

        // This is the format required by the AWS_PROXY lambda integration
        // See https://stackoverflow.com/questions/43708017/aws-lambda-api-gateway-error-malformed-lambda-proxy-response
        return [
            'isBase64Encoded' => $base64Encoding,
            'statusCode' => $psr7Response->getStatusCode(),
            $headersKey => $headers,
            'body' => $base64Encoding ? base64_encode($body) : $body,
        ];
    }

    private static function parseBodyAndUploadedFiles(HttpRequestEvent $event): array
    {
        $bodyString = $event->getBody();
        $files = [];
        $parsedBody = null;
        $contentType = $event->getContentType();
        if ($contentType !== null && $event->getMethod() === 'POST') {
            if ($contentType === 'application/x-www-form-urlencoded') {
                parse_str($bodyString, $parsedBody);
            } else {
                $document = new Part("Content-type: $contentType\r\n\r\n" . $bodyString);
                if ($document->isMultiPart()) {
                    $parsedBody = [];
                    foreach ($document->getParts() as $part) {
                        if ($part->isFile()) {
                            $tmpPath = tempnam(sys_get_temp_dir(), 'bref_upload_');
                            if ($tmpPath === false) {
                                throw new RuntimeException('Unable to create a temporary directory');
                            }
                            file_put_contents($tmpPath, $part->getBody());
                            $file = new UploadedFile(
                                $tmpPath,
                                filesize($tmpPath),
                                UPLOAD_ERR_OK,
                                $part->getFileName(),
                                $part->getMimeType()
                            );

                            self::parseKeyAndInsertValueInArray($files, $part->getName(), $file);
                        } else {
                            self::parseKeyAndInsertValueInArray($parsedBody, $part->getName(), $part->getBody());
                        }
                    }
                }
            }
        }
        return [$files, $parsedBody];
    }

    /**
     * Parse a string key like "files[id_cards][jpg][]" and do $array['files']['id_cards']['jpg'][] = $value
     *
     * @param mixed $value
     */
    private static function parseKeyAndInsertValueInArray(array &$array, string $key, $value): void
    {
        if (strpos($key, '[') === false) {
            $array[$key] = $value;

            return;
        }

        $parts = explode('[', $key); // files[id_cards][jpg][] => [ 'files',  'id_cards]', 'jpg]', ']' ]
        $pointer = &$array;

        foreach ($parts as $k => $part) {
            if ($k === 0) {
                $pointer = &$pointer[$part];

                continue;
            }

            // Skip two special cases:
            // [[ in the key produces empty string
            // [test : starts with [ but does not end with ]
            if ($part === '' || substr($part, -1) !== ']') {
                // Malformed key, we use it "as is"
                $array[$key] = $value;

                return;
            }

            $part = substr($part, 0, -1); // The last char is a ] => remove it to have the real key

            if ($part === '') { // [] case
                $pointer = &$pointer[];
            } else {
                $pointer = &$pointer[$part];
            }
        }

        $pointer = $value;
    }

}
