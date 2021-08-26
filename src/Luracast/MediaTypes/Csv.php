<?php

namespace Luracast\Restler\MediaTypes;

use Luracast\Restler\Contracts\ResponseMediaTypeInterface;
use Luracast\Restler\Contracts\StreamingRequestMediaTypeInterface;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\ResponseHeaders;

class Csv extends MediaType implements StreamingRequestMediaTypeInterface, ResponseMediaTypeInterface
{
    public const MIME = 'text/csv';
    public const EXTENSION = 'csv';
    public static string $delimiter = ',';
    public static string $enclosure = '"';
    public static string $escape = '\\';
    public static ?bool $haveHeaders = null;

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

        $decoded = $this->convert->toArray($decoded);

        return $decoded;
    }

    protected static function getRow($data, $keys = false)
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
     * @return array {@type associative}
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

        $decoded = $this->convert->toArray($decoded);

        return $decoded;
    }

    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false)
    {
        $data = $this->convert->toArray($data);
        if (is_array($data) && array_values($data) == $data) {
            //if indexed array
            $lines = array();
            $row = array_shift($data);
            if (array_values($row) != $row) {
                $lines[] = static::putRow(array_keys($row));
            }
            $lines[] = static::putRow(array_values($row));
            foreach ($data as $row) {
                $lines[] = static::putRow(array_values($row));
            }

            return implode(PHP_EOL, $lines) . PHP_EOL;
        }
        throw new HttpException(500, 'Unsupported data for ' . strtoupper(static::EXTENSION) . ' MediaType');
    }

    protected static function putRow($data)
    {
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, $data, static::$delimiter, static::$enclosure);
        rewind($fp);
        $data = fread($fp, 1_048_576);
        fclose($fp);

        return rtrim($data, PHP_EOL);
    }
}
