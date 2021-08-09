<?php
namespace Luracast\Restler\MediaTypes;


class Tsv extends Csv
{
    public const MIME = 'text/tab-separated-values';
    public const EXTENSION = 'tsv';
    public static string $delimiter = "\t";
    public static string $enclosure = '"';
    public static string $escape = '\\';
    public static ?bool $haveHeaders = null;
}
