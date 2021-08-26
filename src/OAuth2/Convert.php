<?php

namespace OAuth2;


use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Utils\ClassName;
use Luracast\Restler\Utils\Text;
use Psr\Http\Message\ResponseInterface as PSRResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class Convert
{
    final public static function fromPSR7(ServerRequestInterface $psrRequest): Request
    {
        $body = $psrRequest->getBody();
        $contents = $body->getContents();
        if ($body->isSeekable()) {
            $body->rewind();
        } elseif ($body->isWritable()) {
            $body->write($contents);
        }

        $type = $psrRequest->getHeaderLine('Content-Type');
        $data = [];
        switch ($psrRequest->getMethod()) {
            case'PUT':
            case 'POST':
                $data = (array)$psrRequest->getParsedBody();
                if (empty($data)) {
                    if (Text::beginsWith($type, 'application/json')) {
                        $data = json_decode($contents, true);
                    } elseif (Text::beginsWith($type, 'multipart/form-data')) {
                        $data = static::multipartFormData($contents, $type);
                    } else {
                        parse_str($contents, $data);
                    }
                }
        }
        if (!$data) {
            $data = [];
        }
        return new Request(
            (array)$psrRequest->getQueryParams(),
            $data,
            $psrRequest->getAttributes(),
            $psrRequest->getCookieParams(),
            static::convertUploadedFiles($psrRequest->getUploadedFiles()),
            static::serverParameters($psrRequest),
            $contents,
            static::cleanupHeaders($psrRequest->getHeaders())
        );
    }

    public static function multipartFormData(string $rawBody, string $contentType): array
    {
        $post = [];
        $files = [];
        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $contentType, $matches);
        $boundary = $matches[1];

        // split content by boundary and get rid of last -- element
        $blocks = preg_split("/-+$boundary/", $rawBody);
        array_pop($blocks);

        $key = -1;
        foreach ($blocks as $boundary_data_buffer) {
            if (empty($boundary_data_buffer)) {
                continue;
            }
            list($boundary_header_buffer, $boundary_value) = explode("\r\n\r\n", $boundary_data_buffer, 2);
            // Remove \r\n from the end of buffer.
            $boundary_value = substr($boundary_value, 0, -2);
            $key++;
            foreach (explode("\r\n", $boundary_header_buffer) as $item) {
                if (empty($item)) {
                    continue;
                }
                list($header_key, $header_value) = explode(": ", $item);
                $header_key = strtolower($header_key);
                switch ($header_key) {
                    case "content-disposition":
                        // Is file data.
                        if (preg_match('/name="(.*?)"; filename="(.*?)"$/', $header_value, $match)) {
                            // Parse $_FILES.
                            $files[$key] = array(
                                'name' => $match[1],
                                'file_name' => $match[2],
                                'file_data' => $boundary_value,
                                'file_size' => strlen($boundary_value),
                            );
                            continue 2;
                        } elseif (preg_match('/name="(.*?)"$/', $header_value, $match)) {
                            $post[$match[1]] = $boundary_value;
                        }
                        break;
                    case "content-type":
                        // add file_type
                        $files[$key]['file_type'] = trim($header_value);
                        break;
                }
            }
        }
        $post['files'] = $files;
        return $post;
    }

    /**
     * Convert a PSR-7 uploaded files structure to a $_FILES structure.
     *
     * @param array $uploadedFiles Array of file objects.
     *
     * @return array
     */
    private static function convertUploadedFiles(array $uploadedFiles): array
    {
        $files = [];
        foreach ($uploadedFiles as $name => $uploadedFile) {
            if (!is_array($uploadedFile)) {
                $files[$name] = self::convertUploadedFile($uploadedFile);
                continue;
            }
            $files[$name] = [];
            foreach ($uploadedFile as $file) {
                $files[$name][] = self::convertUploadedFile($file);
            }
        }
        return $files;
    }

    private static function convertUploadedFile(UploadedFileInterface $uploadedFile): array
    {
        return [
            'name' => $uploadedFile->getClientFilename(),
            'type' => $uploadedFile->getClientMediaType(),
            'size' => $uploadedFile->getSize(),
            'tmp_name' => $uploadedFile->getStream()->getMetadata('uri'),
            'error' => $uploadedFile->getError(),
        ];
    }

    private static function serverParameters(ServerRequestInterface $psrRequest): array
    {
        $params = $psrRequest->getServerParams();
        if (!isset($params['REQUEST_METHOD'])) {
            $params['REQUEST_METHOD'] = $psrRequest->getMethod();
            $params['PATH_INFO'] = $params['REQUEST_URI'] = $psrRequest->getUri()->getPath();
            $params['CONTENT_TYPE'] = $psrRequest->getHeaderLine('Content-Type');
            $params['CONTENT_LENGTH'] = $psrRequest->getHeaderLine('Content-Length');
        }
        return $params;
    }

    /**
     * Helper method to clean header keys and values.
     *
     * Slim will convert all headers to Camel-Case style. There are certain headers such as PHP_AUTH_USER that the
     * OAuth2 library requires CAPS_CASE format. This method will adjust those headers as needed.  The OAuth2 library
     * also does not expect arrays for header values, this method will implode the multiple values with a ', '
     *
     * @param array $uncleanHeaders The headers to be cleaned.
     *
     * @return array The cleaned headers
     */
    private static function cleanupHeaders(array $uncleanHeaders = []): array
    {
        $cleanHeaders = [];
        $headerMap = [
            'Php-Auth-User' => 'PHP_AUTH_USER',
            'Php-Auth-Pw' => 'PHP_AUTH_PW',
            'Php-Auth-Digest' => 'PHP_AUTH_DIGEST',
            'Auth-Type' => 'AUTH_TYPE',
            'HTTP_AUTHORIZATION' => 'AUTHORIZATION',
        ];
        foreach ($uncleanHeaders as $key => $value) {
            if (array_key_exists($key, $headerMap)) {
                $key = $headerMap[$key];
            }
            $cleanHeaders[$key] = is_array($value) ? implode(', ', $value) : $value;
        }
        return $cleanHeaders;
    }

    /**
     * @throws HttpException
     */
    final public static function toPSR7(Response $oauthResponse): PSRResponse
    {
        $headers = [];
        foreach ($oauthResponse->getHttpHeaders() as $key => $value) {
            $headers[$key] = explode(', ', $value);
        }
        $body = '';

        if (!empty($oauthResponse->getParameters())) {
            $body = $oauthResponse->getResponseBody();
        }
        $class = ClassName::get(PSRResponse::class);
        return new $class($oauthResponse->getStatusCode(), $headers, (string)$body);
    }

}
