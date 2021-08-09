<?php


namespace Luracast\Restler\Cache;


use DateInterval;
use DateTime;
use Luracast\Restler\Defaults;

class HumanReadable extends Base
{
    /**
     * @var string path of the folder to hold cache files
     */
    public static ?string $cacheDirectory = null;

    public function __construct()
    {
        if (is_null(self::$cacheDirectory)) {
            self::$cacheDirectory = Defaults::$cacheDirectory;
        }
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $file = $this->_file($key);
        if (!file_exists($file)) {
            return $default;
        }
        $value = include($file);
        if (($expires = $value['expires'] ?? false)) {
            if (time() <= $expires) {
                $this->$this->delete($key);
            } else {
                $value['data'] ?? $default;
            }
        } else {
            return $value['data'] ?? $default;
        }
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        $timestamp = $this->timestamp($ttl);
        $value = ['expires' => $timestamp, 'data' => $value];
        if (is_array($value)) {
            $s = '$o = array();' . PHP_EOL . PHP_EOL;
            $s .= '// ** THIS IS AN AUTO GENERATED FILE.'
                . ' DO NOT EDIT MANUALLY ** ';
            foreach ($value as $k => $v) {
                $s .= PHP_EOL . PHP_EOL .
                    "//==================== $k ===================="
                    . PHP_EOL . PHP_EOL;
                if (is_array($v)) {
                    $s .= '$o[\'' . $k . '\'] = array();';
                    foreach ($v as $ke => $va) {
                        $s .= PHP_EOL . PHP_EOL . "//==== $k $ke ===="
                            . PHP_EOL . PHP_EOL;
                        $s .= '$o[\'' . $k . '\'][\'' . $ke . '\'] = ' .
                            str_replace(
                                '  ',
                                '    ',
                                var_export($va, true)
                            ) . ';';
                    }
                } else {
                    $s .= '$o[\'' . $k . '\'] = '
                        . var_export($v, true) . ';';
                }
            }
            $s .= PHP_EOL . 'return $o;';
        } else {
            $s = 'return ' . var_export($value, true) . ';';
        }
        $file = $this->_file($key);
        $r = @file_put_contents($file, "<?php $s");
        @chmod($file, 0777);
        if ($r === false) {
            $this->throwException();
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        return @unlink($this->_file($key));
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        array_map('unlink', array_filter((array)glob(static::$cacheDirectory . '/*.php')));
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return file_exists($this->_file($key));
    }

    private function timestamp($ttl)
    {
        if (is_null($ttl)) {
            return false;
        }
        $now = (new DateTime('now'));
        if ($ttl instanceof DateInterval) {
            return $now->add($ttl)->getTimestamp();
        } elseif (is_int($ttl)) {
            return $now->getTimestamp() + $ttl;
        }
        return 0;
    }

    private function _file($name)
    {
        return self::$cacheDirectory . '/' . $name . '.php';
    }

    private function throwException()
    {
        throw new InvalidArgument(
            'The cache directory `'
            . self::$cacheDirectory . '` should exist with write permission.'
        );
    }
}
