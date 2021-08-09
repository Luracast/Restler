<?php


namespace Luracast\Restler\Session;


use Exception;
use SessionHandlerInterface;
use SessionIdInterface;

use function bin2hex;
use function random_bytes;

class FileSessionHandler implements SessionHandlerInterface, SessionIdInterface
{
    private ?string $savePath = null;
    private array $data = [];

    public function __construct(string $savePath)
    {
        $this->open($savePath, '');
    }

    public function open($savePath, $sessionName)
    {
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777, true);
        }

        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $this->data[$id] = (string)@file_get_contents("$this->savePath/sess_$id");
        return $this->data[$id];
    }

    public function write($id, $data)
    {
        $file = "$this->savePath/sess_$id";
        if (isset($this->data[$id]) && $this->data[$id] == $data) {
            return touch($file);
        }
        return file_put_contents($file, $data) === false ? false : true;
    }

    public function destroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Create session ID
     * @link https://php.net/manual/en/sessionidinterface.create-sid.php
     * @return string
     * @throws Exception
     */
    public function create_sid()
    {
        return bin2hex(random_bytes(32));
    }
}
