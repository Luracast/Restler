<?php

namespace Luracast\Restler\Utils;

use Exception;
use Luracast\Restler\Exceptions\HttpException;

/**
 * Parses the PHPDoc comments for metadata. Inspired by `Documentor` code base.
 */
class CommentParser
{
    /**
     * separator for type definitions
     */
    public const TYPE_SEPARATOR = '|';
    /**
     * character sequence used to escape \@
     */
    public const ESCAPE_SEQUENCE_START = '\\@';
    /**
     * character sequence used to escape end of comment
     */
    public const ESCAPE_SEQUENCE_END = '{@*}';
    /**
     * name for the embedded data
     */
    public static string $embeddedDataName = 'properties';
    /**
     * Regular Expression pattern for finding the embedded data and extract
     * the inner information. It is used with preg_match.
     */
    public static string $embeddedDataPattern
        = '/```(\w*)[\s]*(([^`]*`{0,2}[^`]+)*)```/ms';
    /**
     * Pattern will have groups for the inner details of embedded data
     * this index is used to locate the data portion.
     */
    public static int $embeddedDataIndex = 2;
    /**
     * Delimiter used to split the array data.
     *
     * When the name portion is of the embedded data is blank auto detection
     * will be used and if URLEncodedFormat is detected as the data format
     * the character specified will be used as the delimiter to find split
     * array data.
     */
    public static string $arrayDelimiter = ',';
    /**
     * @var array annotations that support array value
     */
    public static array $allowsArrayValue = [
        'choice' => true,
        'select' => true,
        'properties' => true,
    ];
    public static array $typeFixes = [
        'integer' => 'int',
        'boolean' => 'bool',
    ];
    /**
     * Comment information is parsed and stored in to this array.
     */
    private array $_data = [];

    /**
     * Parse the comment and extract the data.
     *
     * @static
     *
     * @param      $comment
     * @param bool $isPhpDoc
     *
     * @return array associative array with the extracted values
     * @throws Exception
     */
    public static function parse($comment, bool $isPhpDoc = true): array
    {
        $p = new self();
        if (empty($comment)) {
            return $p->_data;
        }

        if ($isPhpDoc) {
            $comment = self::removeCommentTags($comment);
        }

        $p->extractData($comment);
        return $p->_data;
    }

    /**
     * Removes the comment tags from each line of the comment.
     *
     * @static
     *
     * @param string $comment PhpDoc style comment
     *
     * @return string comments with out the tags
     */
    public static function removeCommentTags(string $comment): string
    {
        $pattern = '/(^\/\*\*)|(^\s*\**[ \/]?)|\s(?=@)|\s\*\//m';
        return preg_replace($pattern, '', $comment);
    }

    /**
     * Extracts description and long description, uses other methods to get
     * parameters.
     *
     * @param $comment
     *
     * @return array
     * @throws Exception
     */
    private function extractData(string $comment): array
    {
        //to use @ as part of comment we need to
        $comment = str_replace(
            [self::ESCAPE_SEQUENCE_END, self::ESCAPE_SEQUENCE_START],
            ['*/', '@'],
            $comment
        );

        $summary = [];
        $description = [];
        $params = [];

        $mode = 0; // extract short summary;
        $comments = preg_split("/(\r?\n)/", $comment);
        // remove first blank line;
        if (empty($comments[0])) {
            array_shift($comments);
        }
        $addNewline = false;
        foreach ($comments as $line) {
            $line = trim($line);
            $newParam = false;
            if (empty ($line)) {
                if ($mode == 0) {
                    $mode++;
                } else {
                    $addNewline = true;
                }
                continue;
            } elseif ($line[0] == '@') {
                $mode = 2;
                $newParam = true;
            }
            switch ($mode) {
                case 0 :
                    $summary[] = $line;
                    if (count($summary) > 3) {
                        // if more than 3 lines take only first line
                        $description = $summary;
                        $summary[] = array_shift($description);
                        $mode = 1;
                    } elseif (substr($line, -1) == '.') {
                        $mode = 1;
                    }
                    break;
                case 1 :
                    if ($addNewline) {
                        $line = ' ' . $line;
                    }
                    $description[] = $line;
                    break;
                case 2 :
                    $newParam
                        ? $params[] = $line
                        : $params[count($params) - 1] .= ' ' . $line;
            }
            $addNewline = false;
        }
        $summary = implode(' ', $summary);
        $description = implode(' ', $description);
        $summary = preg_replace('/\s+/msu', ' ', $summary);
        $description = preg_replace('/\s+/msu', ' ', $description);
        list(
            $summary, $d1
            )
            = $this->parseEmbeddedData($summary);
        list(
            $description, $d2
            )
            = $this->parseEmbeddedData($description);
        $this->_data = compact('summary', 'description');
        $d2 += $d1;
        if (!empty($d2)) {
            $this->_data[self::$embeddedDataName] = $d2;
        }
        foreach ($params as $key => $line) {
            list(, $param, $value) = preg_split('/@|\s/', $line, 3)
            + ['', '', ''];
            list($value, $embedded) = $this->parseEmbeddedData($value);
            $value = array_filter(preg_split('/\s+/msu', $value), 'strlen');
            $this->parseParam($param, $value, $embedded);
        }
        return $this->_data;
    }

    /**
     * Parses the inline php doc comments and embedded data.
     *
     * @param string $subject
     *
     * @return array
     * @throws Exception
     */
    private function parseEmbeddedData(string $subject): array
    {
        $data = [];

        //parse {@pattern } tags specially
        while (preg_match('|(?s-m)({@pattern (/.+/[imsxuADSUXJ]*)})|', $subject, $matches)) {
            $subject = str_replace($matches[0], '', $subject);
            $data['pattern'] = $matches[2];
        }
        while (preg_match('/{@(\w+)\s?([^}]*)}/ms', $subject, $matches)) {
            $subject = str_replace($matches[0], '', $subject);
            $key = $matches[1];
            $val = $matches[2];
            if ($key == 'pattern') {
                throw new Exception(
                    'Inline pattern tag should follow {@pattern /REGEX_PATTERN_HERE/} format and can optionally include PCRE modifiers following the ending `/`'
                );
            } elseif (isset(static::$allowsArrayValue[$key])) {
                $val = explode(static::$arrayDelimiter, $val);
            } elseif ($val == 'true' || $val == 'false') {
                $val = $val == 'true';
            } elseif ($val == '') {
                $val = true;
            } elseif ($key == 'required') {
                $val = explode(static::$arrayDelimiter, $val);
            } elseif ($key == 'type') {
                $val = explode(self::TYPE_SEPARATOR, $val);
            } elseif ($key == 'min' || $key == 'max') {
                $val = explode(self::TYPE_SEPARATOR, $val);
                if (count($val) < 2) {
                    $val = [null, empty($val[0]) ? null : Type::numericValue($val[0])];
                } else {
                    $val[0] = empty($val[0]) ? null : (int)$val[0];
                    $val[1] = empty($val[1]) ? null : Type::numericValue($val[1]);
                }
            }
            $data[$key] = $val;
        }

        while (preg_match(self::$embeddedDataPattern, $subject, $matches)) {
            $subject = str_replace($matches[0], '', $subject);
            $str = $matches[self::$embeddedDataIndex];
            // auto detect
            if ($str[0] == '{') {
                $d = json_decode($str, true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new Exception(
                        'Error parsing embedded JSON data'
                        . " $str"
                    );
                }
                $data = $d + $data;
            } else {
                parse_str($str, $d);
                //clean up
                $d = array_filter($d);
                foreach ($d as $key => $val) {
                    $kt = trim($key);
                    if ($kt != $key) {
                        unset($d[$key]);
                        $key = $kt;
                        $d[$key] = $val;
                    }
                    if (is_string($val)) {
                        if ($val == 'true' || $val == 'false') {
                            $d[$key] = $val == 'true';
                        } else {
                            $val = explode(self::$arrayDelimiter, $val);
                            if (count($val) > 1) {
                                $d[$key] = $val;
                            } else {
                                $d[$key] =
                                    preg_replace(
                                        '/\s+/msu',
                                        ' ',
                                        $d[$key]
                                    );
                            }
                        }
                    }
                }
                $data = $d + $data;
            }
        }
        return [$subject, $data];
    }

    /**
     * Parse parameters that begin with (at)
     *
     * @param       $param
     * @param array $value
     * @param array $embedded
     */
    private function parseParam($param, array $value, array $embedded): void
    {
        $data = &$this->_data;
        $allowMultiple = false;
        switch ($param) {
            case 'param' :
            case 'property' :
            case 'property-read' :
            case 'property-write' :
                $value = $this->formatParam($value);
                $allowMultiple = true;
                break;
            case 'var' :
                $value = $this->formatVar($value);
                break;
            case 'return' :
                $value = $this->formatReturn($value);
                break;
            case 'class' :
                $data = &$data[$param];
                list ($param, $value) = $this->formatClass($value);
                break;
            case 'format':
            case 'request-format':
            case 'response-format':
                $value = explode(',', $value[0]);
                break;
            case 'access' :
                $value = reset($value);
                break;
            case 'expires' :
            case 'status' :
            case 'throttle' :
                $value = intval(reset($value));
                break;
            case 'throws' :
                $value = $this->formatThrows($value);
                $allowMultiple = true;
                break;
            case 'author':
                $value = $this->formatAuthor($value);
                $allowMultiple = true;
                break;
            case 'deprecated':
                $value = true;
                break;
            case 'url':
            case 'header':
            case 'link':
            case 'example':
            case 'cache':
                /** @noinspection PhpMissingBreakStatementInspection */
            case 'todo':
                $allowMultiple = true;
            //don't break, continue with code for default:
            default :
                $value = implode(' ', $value);
        }
        if (!empty($embedded)) {
            if (is_string($value)) {
                $value = ['description' => $value];
            }
            if (!empty($embedded['type'])) {
                if ('array' === $value['type'][0]) {
                    $this->typeFix($embedded['type'], 'associative');
                    if ('associative' === $embedded['type'][0] || 'indexed' === $embedded['type'][0]) {
                        $embedded['format'] = $embedded['type'][0];
                        unset($embedded['type']);
                    }
                } else {
                    $embedded['format'] = $embedded['type'][0];
                    unset($embedded['type']);
                }
            }
            $value[self::$embeddedDataName] = $embedded;
        }
        if (empty ($data[$param])) {
            $data[$param] = $allowMultiple ? [$value] : $value;
        } elseif ($allowMultiple) {
            $data[$param][] = $value;
        } else {
            if (!is_string($value) && isset($value[self::$embeddedDataName])
                && isset($data[$param][self::$embeddedDataName])
            ) {
                $data[$param][self::$embeddedDataName] =
                    $value[self::$embeddedDataName] + $data[$param][self::$embeddedDataName];
            }
            if (!is_array($data[$param])) {
                $data[$param] = ['description' => (string)$data[$param]];
            }
            if (is_array($value)) {
                $data[$param] = $value + $data[$param];
            }
        }
    }

    private function formatParam(array $value): array
    {
        $r = [];
        $data = array_shift($value);
        if (empty($data)) {
            $r['type'] = ['mixed'];
        } elseif ($data[0] == '$') {
            $r['name'] = substr($data, 1);
            $r['type'] = ['mixed'];
        } else {
            $data = explode(self::TYPE_SEPARATOR, $data);
            $r['type'] = $data;

            $data = array_shift($value);
            if (!empty($data) && $data[0] == '$') {
                $r['name'] = substr($data, 1);
            }
        }
        $this->typeAndDescription($r, $value);
        return $r;
    }

    private function typeAndDescription(&$r, array $value, string $default = 'array'): void
    {
        if (count($r['type'])) {
            if (Text::endsWith($r['type'][0], '[]')) {
                $r[static::$embeddedDataName]['type'] = [substr($r['type'][0], 0, -2)];
                $r['type'][0] = 'array';
            } else {
                $this->typeFix($r['type'], $default);
            }
        }
        if ($value) {
            $r['description'] = implode(' ', $value);
        }
    }

    private function typeFix(array &$type, string $default = 'string'): void
    {
        $length = count($type);
        $type = str_ireplace(array_keys(static::$typeFixes), array_values(static::$typeFixes), $type);
        if ($length) {
            if ('null' === $type[0]) {
                if (1 == $length) {
                    array_unshift($type, $default);
                } else {
                    array_shift($type);
                    array_push($type, 'null');
                }
            }
        }
    }

    private function formatVar(array $value): array
    {
        $r = [];
        $data = array_shift($value);
        if (empty($data)) {
            $r['type'] = ['mixed'];
        } elseif ($data[0] == '$') {
            $r['name'] = substr($data, 1);
            $r['type'] = ['mixed'];
        } else {
            $data = explode(self::TYPE_SEPARATOR, $data);
            $r['type'] = $data;
        }
        $this->typeAndDescription($r, $value);
        return $r;
    }

    private function formatReturn(array $value): array
    {
        $data = explode(self::TYPE_SEPARATOR, array_shift($value));
        $r = [
            'type' => $data,
        ];
        $this->typeAndDescription($r, $value);
        return $r;
    }

    private function formatClass(array $value): array
    {
        $param = array_shift($value);

        if (empty($param)) {
            $param = 'Unknown';
        }
        $value = implode(' ', $value);
        return [
            ltrim($param, '\\'),
            ['description' => $value],
        ];
    }

    private function formatThrows(array $value): array
    {
        $exception = count($value) && !is_numeric($value)
            ? array_shift($value)
            : 'Exception';
        $code = count($value) && is_numeric($value)
            ? (HttpException::$codes[array_shift($value)] ?? 500)
            : 500;
        $message = implode(' ', $value);
        if (empty($message)) {
            $message = HttpException::$codes[$code];
        }
        return compact('exception', 'code', 'message');
    }

    private function formatAuthor(array $value): array
    {
        $r = [];
        $email = end($value);
        if ($email[0] == '<') {
            $email = substr($email, 1, -1);
            array_pop($value);
            $r['email'] = $email;
        }
        $r['name'] = implode(' ', $value);
        return $r;
    }
}
