<?php

namespace Luracast\Restler\MediaTypes;

use Generator;
use Luracast\Restler\Contracts\ChunkedResponseMediaTypeInterface;
use Luracast\Restler\Contracts\StreamingRequestMediaTypeInterface;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\ResponseHeaders;

class Csv extends MediaType implements StreamingRequestMediaTypeInterface, ChunkedResponseMediaTypeInterface
{
    public const MIME = 'text/csv';
    public const EXTENSION = 'csv';
    public static string $delimiter = ',';
    public static string $enclosure = '"';
    public static string $escape = '\\';
    public static ?bool $haveHeaders = null;
    /**
     * @var resource
     */
    protected $stream;

    public function decode(string $data)
    {
        $decoded = array();

        if (empty($data)) {
            return $decoded;
        }

        $lines = array_filter(explode(PHP_EOL, $data));

        $keys = false;
        $row = static::getRow(array_shift($lines));

        if (is_null(static::$haveHeaders)) {
            //try to guess with the given data
            static::$haveHeaders = !count(array_filter($row, 'is_numeric'));
        }

        static::$haveHeaders ? $keys = $row : $decoded[] = $row;

        while (($row = static::getRow(array_shift($lines), $keys)) !== false) {
            $decoded [] = $row;
        }

        return $this->convert->toArray($decoded);
    }

    protected static function getRow($data, $keys = false): bool|array
    {
        if (empty($data)) {
            return false;
        }
        $line = str_getcsv(
            $data,
            static::$delimiter,
            static::$enclosure,
            static::$escape
        );

        $row = array();
        foreach ($line as $key => $value) {
            if (is_numeric($value)) {
                $value = floatval($value);
            }
            if ($keys) {
                if (isset($keys [$key])) {
                    $row [$keys [$key]] = $value;
                }
            } else {
                $row [$key] = $value;
            }
        }
        if ($keys) {
            for ($i = count($row); $i < count($keys); $i++) {
                $row[$keys[$i]] = null;
            }
        }

        return $row;
    }

    /**
     * @param $resource resource for a data stream
     *
     * @return array {@format associative}
     */
    public function streamDecode($resource): array
    {
        $decoded = array();

        $keys = false;
        $row = static::getRow(stream_get_line($resource, 0, PHP_EOL));
        if (is_null(static::$haveHeaders)) {
            //try to guess with the given data
            static::$haveHeaders = !count(array_filter($row, 'is_numeric'));
        }

        static::$haveHeaders ? $keys = $row : $decoded[] = $row;

        while (($row = static::getRow(stream_get_line($resource, 0, PHP_EOL), $keys)) !== false) {
            $decoded [] = $row;
        }

        return $this->convert->toArray($decoded);
    }

    /**
     * @throws HttpException
     */
    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false)
    {
        if (is_array($data) && array_values($data) == $data) { //if indexed array
            $row = reset($data);
            $row = $this->convert->toArray($row);
            $this->stream = fopen('php://temp', 'r+');
            if (array_values($row) != $row) {
                $this->putRow(array_keys($row));
            }
            foreach ($data as $row) {
                $this->putRow(array_values($row));
            }
            fputs($this->stream, PHP_EOL);
            return $this->stream;
        }
        throw new HttpException(
            500, 'Unsupported data for '
               . strtoupper(static::EXTENSION) . ' MediaType'
        );
    }

    /**
     * @throws HttpException
     */
    public function encodeChunks(Generator $data, ResponseHeaders $responseHeaders, bool $humanReadable = false)
    {
        try {
            $first = true;
            foreach ($data as $chunk) {
                $chunk = $this->convert->toArray($chunk);
                foreach ($chunk as $row) {
                    if ($first) {
                        $first = false;
                        $this->stream = fopen('php://temp', 'r+');
                        if (array_values($row) != $row) {
                            $this->putRow(array_keys($row));
                        }
                    }
                    $this->putRow(array_values($row));
                }
            }
            fputs($this->stream, PHP_EOL);
            return $this->stream;
        } catch (\Exception $e) {
            throw new HttpException(
                500, 'Unsupported data for '
                   . strtoupper(static::EXTENSION) . ' MediaType. ' . $e->getMessage()
            );
        }
    }

    protected function putRow(array $data): void
    {
        fputcsv($this->stream, $data, static::$delimiter, static::$enclosure);
    }
}
