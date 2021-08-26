<?php
namespace Luracast\Restler\Utils;

use Luracast\Restler\Core;
use Luracast\Restler\Defaults;
use Luracast\Restler\Exceptions\HttpException;
use Psr\Http\Message\ResponseInterface;

class PassThrough
{
    public static array $mimeTypes = array(
        'js' => 'text/javascript',
        'css' => 'text/css',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'html' => 'text/html',
    );

    /**
     * @param string $filePath
     * @param string $IfModifiedSinceHeader
     * @param bool $forceDownload
     * @param float $expires
     * @param bool $isPublic
     * @return ResponseInterface
     * @throws HttpException
     */
    public static function file(
        string $filePath,
        string $IfModifiedSinceHeader = '',
        bool $forceDownload = false,
        float $expires = 0,
        bool $isPublic = true
    ) {
        if (!is_file($filePath)) {
            throw new HttpException(404);
        }
        if (!is_readable($filePath)) {
            throw new HttpException(403);
        }
        $stream = fopen($filePath, 'r');
        if (!$stream) {
            throw new HttpException(403);
        }
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!$mime = static::$mimeTypes[$extension] ?? false) {
            if (!function_exists('finfo_open')) {
                throw new HttpException(
                    500,
                    'Unable to find media type of ' .
                    basename($filePath) .
                    ' either enable fileinfo php extension or update ' .
                    'PassThrough::$mimeTypes to include mime type for ' . $extension .
                    ' extension'
                );
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $filePath);
            static::$mimeTypes[$extension] = $mime;
        }
        if (!is_array(Defaults::$headerCacheControl)) {
            Defaults::$headerCacheControl = array(Defaults::$headerCacheControl);
        }
        $cacheControl = Defaults::$headerCacheControl[0];

        if ($expires > 0) {
            $cacheControl = $isPublic ? 'public' : 'private';
            $cacheControl .= end(Defaults::$headerCacheControl);
            $cacheControl = str_replace('{expires}', $expires, $cacheControl);
            $expires = gmdate('D, d M Y H:i:s \G\M\T', time() + $expires);
        }
        $lastModified = filemtime($filePath);
        $headers = [
            'Content-Type' => $mime,
            'Cache-Control' => $cacheControl,
            'Expires' => $expires,
            'X-Powered-By' => 'Luracast Restler v' . Core::VERSION,
        ];
        $modifiedSince = $IfModifiedSinceHeader;//requestHeader('If-Modified-Since');
        if (
            !empty($modifiedSince) &&
            strtotime($modifiedSince) >= $lastModified
        ) {
            $e = new HttpException(304);
            foreach ($headers as $k => $v) {
                $e->setHeader($k, $v);
            }
            $e->emptyMessageBody = true;
            throw $e;
        }
        $headers['Last-Modified'] = date('r', $lastModified);
        $headers['Content-Length'] = filesize($filePath);
        if ($forceDownload) {
            $headers['Content-Transfer-Encoding'] = 'binary';
            $headers['Content-Disposition'] = 'attachment; filename="' . $filePath . '"';
        }
        $class = ClassName::get(ResponseInterface::class);
        return new $class(200, $headers, $stream);
    }
}
