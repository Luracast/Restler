<?php


class SimpleHttpResponse
{
    /** @var array lowercase version of headers */
    public $headers;
    /** @var string */
    public $body;

    public function __construct(string $body, array $headers = [])
    {
        $this->body = $body;
        $this->headers = array_change_key_case($headers, CASE_LOWER);
    }

    public function header(string $name, $defaultValue = null)
    {
        return $this->headers[strtolower($name)] ?? $defaultValue;
    }
}
