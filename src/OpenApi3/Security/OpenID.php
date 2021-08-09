<?php


namespace Luracast\Restler\OpenApi3\Security;


class OpenID extends Scheme
{
    protected $type = Scheme::TYPE_OPEN_ID_CONNECT;
    protected string $connectUrl;

    public function __construct(string $connectUrl)
    {
        $this->connectUrl = $connectUrl;
    }
}